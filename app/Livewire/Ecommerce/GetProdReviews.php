<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use App\Models\Ecommerce\ProductReview;

#[Lazy()]
class GetProdReviews extends Component
{
    public $prod_reviews = [];

    #[Locked]
    public $product_id;
    public $showAll = false;
    public $loadReviews = 5;
    // public $count_reviews = 0;

    //protected $listeners = ['reviewAdded' => 'getProdReviews'];

    public function mount($product_id)
    {
        $this->product_id = $product_id;
        //$this->getProdReviews();
        //$this->countReviews();
    }

    public function loadMore()
    {
        $this->loadReviews += 5;
    }

    
    //get sg product reviews each product kg dy sinu ng post reviews
    #[Computed()]
    public function getProdReviews()
    {
        return ProductReview::with(['user', 'product'])->where('product_id', $this->product_id)
            ->latest()
            ->take($this->loadReviews)
            ->get();
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

        ]);
    }
}
