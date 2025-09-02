<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\ProductReview;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

#[Lazy()]
class GetProdReviews extends Component
{
    public $prod_reviews = [];

    #[Locked]
    public $product_id;
    public $showAll = false;
    // public $count_reviews = 0;

    //protected $listeners = ['reviewAdded' => 'getProdReviews'];

    public function mount($product_id)
    {
        $this->product_id = $product_id;
        $this->getProdReviews();
        //$this->countReviews();
    }

    //    public function showAllReviews()
    //     {
    //         $this->showAll = true;
    //         $this->prod_reviews = ProductReview::with(['user', 'product'])->where('product_id', $this->product_id)
    //             ->latest()
    //             ->get(); 
    //     }

    // public function newReviewAdded()
    // {
    //     $this->getProdReviews();
    // }

    

    //get sg product reviews each product kg dy sinu ng post reviews

    public function getProdReviews()
    {
        // $this->showAll = false;
        $this->prod_reviews = ProductReview::with(['user', 'product'])->where('product_id', $this->product_id)
            ->latest()
            // ->take(5)
            ->get();


        // $this->count_reviews = $this->prod_reviews->count();

    }
    public function placeholder()
    {
        return view('livewire.ecommerce.lazy_loading.prod-reviews-lazy');
    }

    // //count how many reviews per product
    // public function countReviews(){
    //     $this->count_reviews = ProductReview::where('product_id', $this->product_id)->count();


    // }

    #[Layout('layouts.app')]
    #[Title('Product Reviews')]

    public function render()
    {
        return view('livewire.ecommerce.get-prod-reviews', [
            'prod_reviews' => $this->prod_reviews,
            // 'count_reviews' => $this->count_reviews,
        ]);
    }
}
