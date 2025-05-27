<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class GetCart extends Component
{
    use LivewireAlert;
    public $carts;
    public $quantity;
    public $selectedItems = [];
    public $selectAll = false;

   
    // protected $listeners = ['refreshCartItem' => 'refreshCartItem', 'cartUpdated' => 'getCarts'];
    protected $listeners = ['cartUpdated' => 'getCarts'];

    public function mount()
    {
        $this->getCarts();
        
    }


    public function updatedSelectedItems()
    {
       
        $this->dispatch('updateCheckoutItems', items: $this->selectedItems);
    }

    //kwa sng add to cart guest or logged in user
    public function getCarts()
    {
        $this->carts = Cart::with(['product.images', 'user'])
            ->where(fn ($query) => Auth::check()
                ? $query->where('user_id', Auth::id())
                : $query->where('session_id', Session::getId())
            )->get();
    }

    public function increaseQuantity($cart_id)
    {
        $cart = Cart::with('product')->find($cart_id);

        if ($cart && $cart->product && $cart->quantity < $cart->product->prod_quantity) {
            $cart->increment('quantity', 1);
        } else {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Not enough stock available!',
            ]);
        }

        $this->getCarts(); // Keep this if needed to recalculate totals
    // $this->dispatch('cartUpdated'); // Needed for frontend update (if used)
    }

    public function decreaseQuantity($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart) {
            if ($cart->quantity > 1) {
                $cart->decrement('quantity', 1);
            } else {
                $cart->delete();

                $this->alert('success', '', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                    'text' => 'Product removed from cart',
                ]);
            }
        }

        $this->getCarts(); // Keep this if you need to recalculate totals
        //$this->dispatch('cartUpdated');
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
            return redirect()->route('login');
        }
        //    dd($this->selectedItems);
        if(empty($this->selectedItems)){
            $this->alert('warning','', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'No selected items!',
            ]);
            return;
        }
        session()->put('selected_checkout_items', $this->selectedItems);
    
            return redirect()->route('checkout');
        }

    
    #[Layout('layouts.app')]
    #[Title('Cart')]
    public function render()
    {
        return view('livewire.ecommerce.get-cart', [
            'carts' => $this->carts
        ]);
    }
}
