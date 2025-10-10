<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Locked;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Ecommerce\ProductImage as ProductVariantSize;

class ProductVariant extends Component
{
    use LivewireAlert;


    #[Locked]
    public $product_id;
    public $product;

    #[Locked]
    public $selectedSize;

    #[Locked]
    public $price = 0;

    private $isLoggedIn;
    private $sessionId;

    public function mount($product_id)
    {
        $this->product_id = $product_id;
        // $this->isLoggedIn = auth()->check() ? auth()->user()->id : null;
        // $this->sessionId = auth()->check() ? null : Session::getId();
        $this->getVariantProduct();
    }

    public function getVariantProduct()
    {
        $this->product = Product::find($this->product_id);
    }

    public function selectSize($size)
    {
        // dd($size);
        if ($this->selectedSize == $size) {
            $this->selectedSize = null;
            $this->price = 0;
        } else {
            $this->selectedSize = $size;
            $this->price = ProductVariantSize::find($size)?->price;
        }
    }

    protected $rules = [
        'selectedSize' => 'required|exists:product_images,id',
    ];

    public function addToCart()
    {
        $this->validate();
        $this->isLoggedIn = auth()->check() ? auth()->user()->id : null;
        $this->sessionId = auth()->check() ? null : Session::getId();


        $existingCartItem = $this->getExistingCartItem($this->product);

        if ($existingCartItem) {
            $this->updateExistingCartItem($existingCartItem, $this->product);
        } else {
            $this->createNewCartItem();
            $this->alert('success', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Product added to cart',
                'showCloseButton' => true,
            ]);
        }
        $this->selectedSize = null;
        $this->price = 0;

        $this->dispatch('cartUpdated');
    }

    protected function getExistingCartItem($product)
    {
        $size = ProductVariantSize::find($this->selectedSize)->sizes;
        return Cart::where('product_id', $product->id)
            ->where('size', $size)
            ->where(function ($query) {
                $query->when(
                    $this->isLoggedIn,
                    fn($q) => $q->where('user_id', $this->isLoggedIn),
                    fn($q) => $q->where('session_id', $this->sessionId)
                );
            })
            ->first();
    }

    protected function updateExistingCartItem(Cart $cart, Product $product_var)
    {
        $quantityToAdd = 1;

        //check if stock is enough sa variant ara sa images table
        if ($cart->quantity + $quantityToAdd > $product_var->images()->find($this->selectedSize)->quantity) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Not enough stock available!',
                'showCloseButton' => true,
            ]);

            return;
        }

        $cart->increment('quantity', $quantityToAdd);

        $this->alert('success', '', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Product added to cart',
            'showCloseButton' => true,
        ]);
    }


    protected function createNewCartItem()
    {
        // dd($this->selectedSize);
        $size = ProductVariantSize::find($this->selectedSize)->sizes;
        Cart::create([
            'product_id' => $this->product_id,
            'user_id' => $this->isLoggedIn,
            'session_id' => $this->sessionId,
            'quantity' => 1,
            'size' => $size

        ]);
    }

    public function resetModal()
    {
        $this->selectedSize = null;
        $this->price = 0;
    }
    public function render()
    {
        return view(
            'livewire.ecommerce.product-variant',
            [
                'product' => $this->product
            ]
        );
    }

    public function buyNowWithSize()
    {
        if (! Auth::check()) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'You must login first',
            ]);
            return redirect()->route('login');
        }
        $selectedVariant = ProductVariantSize::find($this->selectedSize);
        session()->put('buy_now_product', [
            'product_id' => $this->product_id,
            'variant_id' => $this->selectedSize,
            'size' => $selectedVariant ? $selectedVariant->sizes : null,
            'quantity' => 1
        ]);

        session()->forget('selected_checkout_items');

        //return redirect()->route('checkout');
        return $this->redirect(route('checkout'));
    }
}
