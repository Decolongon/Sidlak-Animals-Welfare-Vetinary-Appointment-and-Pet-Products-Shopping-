<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;

class CartCount extends Component
{


   // protected $listeners = ['cartUpdated' => '$refresh']; // Refresh when cart updates


    #[On('cartUpdated')]
    #[Computed()]
    public function getCartCount()
    {
        if (Auth::check()) {
            //for authenticated users
            return Cart::where('user_id', Auth::id())->count();
        } else {

            return Cart::where('session_id', Session::getId())->count();
        }
    }

   

    // #[Layout('layouts.app')]
    // #[Title('Cart Count')]
    public function render()
    {
        return view('livewire.ecommerce.cart-count');
    }
}
