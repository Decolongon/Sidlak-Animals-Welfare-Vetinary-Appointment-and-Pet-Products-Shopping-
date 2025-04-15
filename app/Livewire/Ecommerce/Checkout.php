<?php

namespace App\Livewire\Ecommerce;


use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Checkout extends Component
{

    public $checkoutItems = [];

    public function mount()
    {
      $this->getCheckoutItems();
        
    }

    public function getCheckoutItems(){
        if(!Auth::user()){
            return redirect()->route('login');
        }
        // $selectedItemIds = session()->pull('selected_checkout_items', []);
        $selectedItemIds = session('selected_checkout_items',[]);
        $this->checkoutItems = Cart::with('product')
                                ->where('user_id', Auth::id())
                                ->whereIn('id', $selectedItemIds)
                                ->get();

     
        // return $this->checkoutItems;
    }

   


    #[Layout('layouts.app')]
    #[Title('Checkout')]
    public function render()
    {
        return view('livewire.ecommerce.checkout',[
           'checkoutItems' => $this->checkoutItems
        ]);
    }
}
