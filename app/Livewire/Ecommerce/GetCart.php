<?php

namespace App\Livewire\Ecommerce;

use Exception;
use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class GetCart extends Component
{
    use LivewireAlert;
    public $carts;
    public $quantity;
    public $product;
    public $selectedItems = [];
    public $selectAll = false;

   
    // protected $listeners = ['refreshCartItem' => 'refreshCartItem', 'cartUpdated' => 'getCarts'];
    // protected $listeners = ['cartUpdated' => 'getCarts'];
    protected $listeners = [
        'cartUpdated' => 'getCarts',
        'removeOutOfStockConfirmed' => 'handleRemoveOutOfStock',
        'confirmedRemoveItem'
    
    ];

    public function mount()
    {   
        
        $this->getCarts();
        // $this->product = new Product(); 
        // $this->getDiscountedPrice($this->product);
        
    }


    public function updatedSelectedItems()
    {
       
        $this->dispatch('updateCheckoutItems', items: $this->selectedItems);
    }

    //kwa sng add to cart guest or logged in user
    public function getCarts()
    {
        $this->carts = Cart::with(['product.images', 'user',
        'product.productDiscounts' => function($query) {
            $query->where('start_at', '<=', now())
                  ->where('end_at', '>=', now());
                
            }
        ])
            ->where(fn ($query) => Auth::check()
                ? $query->where('user_id', Auth::id())
                : $query->where('session_id', Session::getId())
            )->get()
             ->each(function ($cart) {
                        logger()->info('Cart product discounts:', [
                'product_id' => $cart->product->id,
                'discounts' => $cart->product->productDiscounts
            ]);

                $cart->product->setRelation('productDiscounts', $cart->product->productDiscounts->values());
                $this->getDiscountedPrice($cart->product);
            });
    }

    public function increaseQuantity($cart_id)
    {
        //$cart = Cart::with('product')->find($cart_id);
        $cart = $this->carts->where('id', $cart_id)->first();
        if ($cart && $cart->product && $cart->quantity < $cart->product->prod_quantity) {
            $cart->increment('quantity', 1);
          
        } else {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'showConfirmButton' => false,
                'showCloseButton' => true,
                'toast' => true,
                'text' => 'Not enough stock available!',
            ]);
        }

        $this->getCarts(); // Keep this if needed to recalculate totals
    // $this->dispatch('cartUpdated'); // Needed for frontend update (if used)
    }

    public function decreaseQuantity($cart_id)
    {
        // $cart = Cart::find($cart_id);
        $cart = $this->carts->where('id', $cart_id)->first();
        if ($cart) {
            if ($cart->quantity > 1) {
                $cart->decrement('quantity', 1);
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
            $this->alert('error', 'An error occurred: '.$e->getMessage(), [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }
    

    public function toggleSelectAll()
    {
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

        // Delete from DB
        Cart::whereIn('id', $this->selectedItems)->delete();

        // Remove from local state
        $this->carts = $this->carts->reject(
            fn($cart) => in_array($cart->id, $this->selectedItems)
        );

        $this->selectedItems = [];

        $this->alert('success', '', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
            'showCloseButton' => true,
            'text' => 'Selected items removed from cart',
        ]);
    }

    //checkout btn
    public function checkout(){
       
        if(!Auth::check()){
            $this->alert('warning','', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'You must login first',
            ]);
            return redirect()->route('filament.auth.auth.login');
        }
        
       if(empty($this->selectedItems)){
            $this->alert('warning','', [
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
            // Store them temporarily in session or Livewire property
            session()->put('out_of_stock_items', $outOfStockItems->pluck('id')->toArray());
            $this->dispatch('outOfStockDetected');
            return;
        }


            session()->put('selected_checkout_items', $this->selectedItems);
    
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
            'carts' => $this->carts,
            //'getDiscountedPrice' => $this->getDiscountedPrice($this->product),
            
        ]);
    }


    public function getDiscountedPrice( $product)
    {
        $product->discounted_price = null;
        $product->discount_amount = null;
        $product->discount_label = null;

        // Get the first active discount
        $discount = $product->productDiscounts->first();

        if (!$discount || !$discount->pivot) {
            return;
        }

        $type = $discount->pivot->discount_type;
        $value = floatval($discount->pivot->discounted_price);

        if ($type === 'fixed') {
            $product->discount_amount = $value;
            $product->discounted_price = $product->prod_price - $value;
            $product->discount_label = '₱' . number_format($value, 0) . ' off';
        }
        if ($type === 'percent') {
            $discountValue = $product->prod_price * ($value / 100);
            $product->discount_amount = $discountValue;
            $product->discounted_price = $product->prod_price - $discountValue;
            $product->discount_label = number_format($value, 0) . '% off';
        }

        return $product->discounted_price;
       
    }
}