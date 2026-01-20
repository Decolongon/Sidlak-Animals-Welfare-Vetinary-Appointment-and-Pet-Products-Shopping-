<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Models\Ecommerce\Product;
use Livewire\Attributes\Computed;
use App\Livewire\Ecommerce\GetCart;
use App\Traits\AddToCartTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Renderless;

class AddToCartForm extends Component
{
    use LivewireAlert, AddToCartTrait;

    #[Locked]
    public $product_id;

    #[Locked]
    public $session_id;

    #[Locked]
    public $user_id;

    public $quantity;
    public $cartItems;


    public function mount(): void
    {
        $this->initializeSession();
        $this->user_id = Auth::id();
        //$this->migrateGuestCartToUser();
        // $this->getCartItems();
    }

    protected function initializeSession(): void
    {
        if (!Session::isStarted()) {
            Session::start();
        }
        $this->session_id = Session::get('guest_session_id', Session::getId());
    }

    //store ang cart if user is logged in else sa session
    // protected function migrateGuestCartToUser(): void
    // {
    //     if ($this->user_id && Session::has('guest_session_id')) {
    //         Cart::where('session_id', $this->session_id)
    //             ->update([
    //                 'user_id' => $this->user_id,
    //                 'session_id' => null
    //             ]);
    //         Session::forget('guest_session_id');
    //     }
    // }

    #[Renderless]
    public function addToCart(): void
    {
        if ($this->checkRateLimit($this->user_id, $this->session_id)) {
            return;
        }

        $product = Product::find($this->product_id);

        if (!$product) {
            $this->showAlert('warning', 'Product not found!');
            return;
        }

        $cart = $this->getExistingCartItem($product);

        if ($cart) {
            $this->updateExistingCartItem($cart, $product);
        } else {
            $this->createNewCartItem($product);
        }

        $this->dispatch('cartUpdated');
        //$this->showSuccessAlert();
        // $this->getCartItems();
    }

    //get existing cart kng my ara
    #[Computed()]
    public function getExistingCartItem(Product $product): ?Cart
    {
        return Cart::where('product_id', $product->id)
            ->where(function ($query) {
                $query->when(
                    $this->user_id,
                    fn($q) => $q->where('user_id', $this->user_id),
                    fn($q) => $q->where('session_id', $this->session_id)
                );
            })
            ->first();
    }

    // if my existing cart update ang quantity
    protected function updateExistingCartItem(Cart $cart, Product $product): void
    {
        $quantityToAdd = 1;

        if ($cart->quantity + $quantityToAdd > $product->prod_quantity) {
            $this->showAlert('warning', 'Not enough stock available!');
            return;
        }

        $cart->increment('quantity', $quantityToAdd);
        $this->showSuccessAlert();
    }

    //kng wala existing cart create new one
    protected function createNewCartItem(Product $product): void
    {
        Cart::create([
            'product_id' => $product->id,
            'user_id' => $this->user_id ?: null,
            'session_id' => $this->user_id ? null : $this->session_id,
            'quantity' => 1,
        ]);
        $this->showSuccessAlert();
    }

    // get sng tanan na cart items login or guest user mana
    // public function getCartItems(): void
    // {
    //     $this->cartItems = Cart::where(
    //         $this->user_id ? 'user_id' : 'session_id',
    //         $this->user_id ?: $this->session_id
    //     )->orderBy('created_at', 'desc')->get();
    // }


    protected function showAlert(string $type, string $message): void
    {
        $this->alert($type, '', [
            'position' => 'top-end',
            'timer' => 3000,
            'showConfirmButton' => false,
            'showCloseButton' => true,
            'toast' => true,
            'text' => $message,
        ]);
    }

    protected function showSuccessAlert(): void
    {
        $this->alert('success', '', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            // 'showConfirmButton' => true,
            'showCloseButton' => true,
            'text' => 'Product added to cart',
            //'html' => '<span>Product added to cart</span> <button onclick="Swal.close()" style="background:transparent;border:none;color:#999;font-weight:bold;margin-left:8px;cursor:pointer;">×</button>',
            'showConfirmButton' => false
        ]);
    }

    #[Layout('layouts.app')]
    #[Title('Add to Cart')]
    public function render()
    {
        return view('livewire.ecommerce.add-to-cart-form', [
            // 'cartItems' => $this->cartItems
        ]);
    }


    // Rate limiter method
    // private function checkRateLimit(): bool
    // {
    //     $key = 'add-to-cart:' . ($this->user_id ?: $this->session_id);

    //     if (RateLimiter::tooManyAttempts($key, 3)) { // 10 attempts per minute
    //         $seconds = RateLimiter::availableIn($key);
    //         $this->showAlert('error', "Too many attempts. Please try again in {$seconds} seconds.");
    //         return true;
    //     }

    //     RateLimiter::hit($key, 60); // 60 seconds = 1 minute
    //     return false;
    // }
}
