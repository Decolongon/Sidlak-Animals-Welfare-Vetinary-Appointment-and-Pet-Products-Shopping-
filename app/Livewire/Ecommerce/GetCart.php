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
            //$increase = $cart->product->prod_unit == 'kg' ? $cart->product->prod_weight : 1;
            $increase = 1;
            //$cart->quantity += $increase;
            $cart->update(['quantity' =>  $cart->quantity += $increase]);
            //$cart->save()
            
            $this->getCarts();
            //$this->dispatch('cartUpdated');
            // $this->dispatch('refreshCartItem', $cart->id);
        } else {
           // session()->flash('error_message', 'Not enough stock available');
           $this->alert('warning','', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Not enough stock available!',
            ]);
        }
       $this->getCarts();
    //    $this->dispatch('cartUpdated');
    }

    public function decreaseQuantity($cart_id)
    {
        $cart = Cart::find($cart_id);

        if ($cart && $cart->quantity > 1) {
           // $decrease = $cart->product->prod_unit == 'kg' ? $cart->product->prod_weight : 1;
           $decrease = 1;
            //$cart->quantity -= $decrease;
            $cart->update(['quantity' => $cart->quantity -= $decrease]);
            // $cart->save();

            $this->getCarts();
          //$this->dispatch('cartUpdated');
            
            // $this->dispatch('refreshCartItem', $cart->id);
        } else {
            $cart->delete();
            // session()->flash('message', 'Product removed from cart');
            $this->alert('success','', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Product removed from cart',
            ]);
            $this->dispatch('cartUpdated');
            
        }
        $this->getCarts();
       // $this->dispatch('cartUpdated');
    }


    

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->carts->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }

        $this->updatedSelectedItems();
    }

    public function removeSelected()
    {
        if (!empty($this->selectedItems)) {
            // Delete selected cart items
            Cart::whereIn('id', $this->selectedItems)->delete();

            // Remove selected items from local state
            $this->carts = $this->carts->reject(fn ($c) => in_array($c->id, $this->selectedItems));

            $this->selectedItems = [];
            //session()->flash('message', 'Selected items removed from cart');
            $this->alert('success', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Selected items removed from cart'

            ]);
        }
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
