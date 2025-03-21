<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\ProductReview;

class GetProdReviews extends Component
{
    public $prod_reviews = [];
    public $product_id;
    // public $count_reviews = 0;

   

   
    public function mount($product_id){
        $this->product_id = $product_id;
        $this->getProdReviews();
        //$this->countReviews();
    }

    //get sg product reviews each product kg dy sinu ng post reviews
    public function getProdReviews(){
        $this->prod_reviews = ProductReview::with(['user','product'])->where('product_id', $this->product_id)
        ->latest()
        ->get();
     
      
        // $this->count_reviews = $this->prod_reviews->count();
      
    }

    // //count how many reviews per product
    // public function countReviews(){
    //     $this->count_reviews = ProductReview::where('product_id', $this->product_id)->count();

  
    // }

    #[Layout('layouts.app')]
    #[Title('Product Reviews')]
    
    public function render()
    {
        return view('livewire.ecommerce.get-prod-reviews',[
            'prod_reviews' => $this->prod_reviews,
           // 'count_reviews' => $this->count_reviews,
        ]);
    }
}
