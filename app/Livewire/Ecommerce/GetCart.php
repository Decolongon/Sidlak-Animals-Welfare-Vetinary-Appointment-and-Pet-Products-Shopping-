<?php

namespace App\Livewire\Ecommerce;

use Exception;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProductDiscountHelper;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class GetCart extends Component
{
    use LivewireAlert;

    #[Locked]
    public $carts;
    public $quantity;
    public $product;
    public $selectedItems = [];
    public $selectAll = false;


    // protected $listeners = ['refreshCartItem' => 'refreshCartItem', 'cartUpdated' => 'getCarts'];
    // protected $listeners = ['cartUpdated' => 'getCarts'];
    protected $listeners = [
        'cartUpdated' => 'getCarts',
        // 'carts-refreshed' => '$refresh',
        'removeOutOfStockConfirmed' => 'handleRemoveOutOfStock',
        'confirmedRemoveItem', // for button click minus button
        'confirmedRemoveSelectedItems', // multiple selected items confirmation

    ];

    public function mount()
    {

       // $this->getCarts();
        // $this->product = new Product(); 
        // $this->getDiscountedPrice($this->product);
    }


    public function updatedSelectedItems()
    {

        $this->dispatch('updateCheckoutItems', items: $this->selectedItems);
    }

    //kwa sng add to cart guest or logged in user
    #[Computed()]
    public function getCarts()
    {
        $this->carts = Cart::with([
            'product.images',
            'user',
            'product.productDiscounts' => function ($query) {
                $query->ActiveProdDiscount(); // scope ara sa productDiscount model

            }
        ])
            ->where(
                fn($query) => Auth::check()
                    ? $query->where('user_id', Auth::id())
                    : $query->where('session_id', Session::getId())
            )
            ->latest()
            ->get();

        // Load specific variant 
        $this->carts->each(function ($cart) {
            if ($cart->product->prod_unit === 'diff_size' && $cart->size) {
                $cart->variant = $cart->product->images
                    ->where('sizes', $cart->size)
                    ->first();
            }
        });
        $products = $this->carts->pluck('product');
        app(ProductDiscountHelper::class)->calculateDiscountedPrice($products);

        $this->getPrimaryImage($products);
        return $this->carts;
    }


    protected function getPrimaryImage($products)
    {
        foreach ($products as $product) {
            if ($product->images->isNotEmpty()) {
                // Set the primary image dynamically
                $product->primary_image = $product->images
                    ->where('is_primary', true)
                    ->first() ?? $product->images->first();
            } else {
                $product->primary_image = null;
            }
        }
    }

    public function increaseQuantity($cart_id)
    {
        $cart = $this->carts->where('id', $cart_id)->first();

        if (!$cart || !$cart->product) {
            $this->alert('warning', 'Cart item or product not found!', [
                'position' => 'top-end',
                'timer' => 3000,
                'showConfirmButton' => false,
                'showCloseButton' => true,
                'toast' => true,
            ]);
            return;
        }


        $availableQuantity = $this->checkVariant($cart);

        if ($cart->quantity < $availableQuantity) {
            $cart->increment('quantity', 1);
            $this->dispatch('cartUpdated');
        } else {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'showConfirmButton' => false,
                'showCloseButton' => true,
                'toast' => true,
                'text' => 'Not enough stock available',
            ]);
        }

        $this->getCarts();
    }


    protected function checkVariant($cart)
    {
        $availableQuantity = $cart->product->prod_quantity; // Default to product quantity

        //get size from cart
        $size = $cart->size;
        // Check if product has variants (diff_size)
        if ($cart->product->prod_unit === 'diff_size') {
            //get the variant
            $variant = $cart->product->images()
                ->where('sizes', $size)
                ->first();

            if ($variant) {
                $availableQuantity = $variant->quantity;
            }
        }

        return $availableQuantity;
    }

    public function decreaseQuantity($cart_id)
    {
        // $cart = Cart::find($cart_id);
        $cart = $this->carts->where('id', $cart_id)->first();
        if ($cart) {
            if ($cart->quantity > 1) {
                $cart->decrement('quantity', 1);
                $this->dispatch('cartUpdated');
            } else {
                // Show confirmation dialog before removing
                $this->alert('question', 'Are you sure you want to remove this product from your cart?', [
                    'position' => 'center',
                    'timer' => null, // no auto-close
                    'showConfirmButton' => true,
                    'showCancelButton' => true,
                    'confirmButtonText' => 'Yes, remove it',
                    'cancelButtonText' => 'No, keep it',
                    'onConfirmed' => 'confirmedRemoveItem',
                    'inputAttributes' => ['cart_id' => $cart_id],
                    'toast' => false,
                ]);
            }
        }

        $this->getCarts(); // Keep this if you need to recalculate totals
        //$this->dispatch('cartUpdated');
    }


    public function confirmedRemoveItem($data)
    {
        $this->removeItem($data);
    }

    protected function removeItem($data)
    {
        try {
            //check if array
            if (is_array($data)) {
                //check if cart_id naga exist sa array else null it will throw Failed to remove item from cart
                $cart_id = $data['inputAttributes']['cart_id'] ??
                    ($data['data']['inputAttributes']['cart_id'] ?? null);

                // if cart_id naga exist sa array find that item sa cart db kg eh delete 
                if ($cart_id) {
                    $cart = Cart::find($cart_id);

                    if ($cart) {
                        $cart->delete();
                        $this->dispatch('cartUpdated');
                        $this->alert('success', 'Product removed from cart', [
                            'position' => 'top-end',
                            'timer' => 3000,
                            'showConfirmButton' => false,
                            'showCloseButton' => true,
                            'toast' => true,
                        ]);

                        // Refresh the cart data
                        $this->getCarts();
                        return;
                    }
                }
            }

            //fall back message 
            $this->alert('error', 'Failed to remove item from cart', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }


    public function toggleSelectAll()
    {
        //$this->getCarts();
        $this->selectedItems = $this->selectAll
            ? $this->carts->pluck('id')->all()
            : [];

        $this->updatedSelectedItems();
    }


    public function removeSelected()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        // Show confirmation dialog first
        $this->alert('question', '', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'showCloseButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Yes',
            // 'onDismissed' => 'deselectAllItems',
            'cancelButtonText' => 'No keep selected item',
            'onConfirmed' => 'confirmedRemoveSelectedItems',
            'text' => 'Are you sure you want to remove selected items from cart?',
        ]);
    }


    public function confirmedRemoveSelectedItems()
    {
        $this->removingSelectedItems();
    }

    protected function removingSelectedItems()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        $validItems = Cart::whereIn('id', $this->selectedItems)
            ->where(function ($query) {
                Auth::check()
                    ? $query->where('user_id', Auth::id())
                    : $query->where('session_id', Session::getId());
            })
            ->pluck('id')
            ->toArray();

        // Delete from DB
        Cart::whereIn('id', $validItems)->delete();

        // Remove from local state
        $this->carts = $this->carts->reject(
            fn($cart) => in_array($cart->id, $this->selectedItems)
        );

        $this->dispatch('cartUpdated');
        $this->selectedItems = [];

        $this->alert('success', 'Selected items removed from cart', [
            'position' => 'top-end',
            'timer' => 3000,
            'showConfirmButton' => false,
            'showCloseButton' => true,
            'toast' => true,
        ]);
    }

    //checkout btn
    public function checkout()
    {

        if (!Auth::check()) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'You must login first',
            ]);
            return redirect()->route('filament.auth.auth.login');
        }

        if (empty($this->selectedItems)) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'No selected items!',
            ]);
            return;
        }

        $outOfStockItems = Cart::with('product')
            ->whereIn('id', $this->selectedItems)
            ->get()
            ->filter(fn($cart) => $cart->quantity > $cart->product->prod_quantity);

        if ($outOfStockItems->isNotEmpty()) {
            // Store them temporarily in session 
            session()->put('out_of_stock_items', $outOfStockItems->pluck('id')->toArray());
            $this->dispatch('outOfStockDetected');
            return;
        }

        $existingCheckoutItems = session()->get('selected_checkout_items', []);

        // Merge new items with existing ones, avoiding duplicates
        $mergedItems = array_unique(array_merge($existingCheckoutItems, $this->selectedItems));

        // Store the merged array in session
        session()->put('selected_checkout_items', $mergedItems);

        //session()->put('selected_checkout_items', $this->selectedItems);

        return redirect()->route('checkout');
    }


    public function handleRemoveOutOfStock()
    {
        $ids = session()->get('out_of_stock_items', []);

        if (!empty($ids)) {
            Cart::whereIn('id', $ids)->delete();

            // Update local state
            $this->carts = $this->carts->reject(
                fn($cart) => in_array($cart->id, $ids)
            );

            $this->selectedItems = array_diff($this->selectedItems, $ids);

            $this->alert('success', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Out-of-stock items removed.',
            ]);
        }

        session()->forget('out_of_stock_items');

        session()->put('selected_checkout_items', $this->selectedItems);
        return redirect()->route('checkout');
    }


    #[Layout('layouts.app')]
    #[Title('Cart')]
    public function render()
    {
        return view('livewire.ecommerce.get-cart', [
           // 'carts' => $this->carts,
            //'getDiscountedPrice' => $this->getDiscountedPrice($this->product),

        ]);
    }
}
