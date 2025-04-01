<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CheckoutBtn extends Component
{
    use LivewireAlert;
    public $selectedItems = [];


    protected $listeners = ['updateCheckoutItems' => 'setSelectedItems'];

    public function setSelectedItems($items)
    {   
       
        $this->selectedItems = $items;
    }

    // authenticated user direct to checkout page
    public function checkout(){
        // return (!Auth::check()) ? redirect()->route('login')
        //                         : redirect()->route('checkout');

        if(!Auth::check()){
            return redirect()->route('login');
        }
        // dd($this->selectedItems);
        // if(empty($this->selectedItems)){
        //     $this->alert('warning', '',[
        //         'position' => 'top-end',
        //         'timer' => 3000,
        //         'toast' => true,
        //         'text' => 'No items selected'
    
        //     ]);
        //     return;
        // }
        $this->dispatch('checkoutItems', items: $this->selectedItems);

        return redirect()->route('checkout');
    }

    #[Layout('layouts.app')]
    #[Title('Checkout-btn')]
    public function render()
    {
        return view('livewire.ecommerce.checkout-btn');
    }
}
