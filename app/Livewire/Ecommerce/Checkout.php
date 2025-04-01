<?php

namespace App\Livewire\Ecommerce;


use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class Checkout extends Component
{

    public $selectedItems =[];

    protected $listeners = ['checkoutItems' => 'updateItems'];

    public function updateItems($items){
      
        $this->selectedItems = $items;
    }


    #[Layout('layouts.app')]
    #[Title('Checkout')]
    public function render()
    {
        return view('livewire.ecommerce.checkout',[
            'selectedItems' => $this->selectedItems
        ]);
    }
}
