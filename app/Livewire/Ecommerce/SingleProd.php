<?php

namespace App\Livewire\Ecommerce;
use Livewire\Component;
use Livewire\Attributes\Layout;

use Livewire\Attributes\Locked;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Ecommerce\ProductReview;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SingleProd extends Component
{
    use LivewireAlert;

    public $product;
    #[Locked]
    private $prod_slug;

    public $activeTab = 'description';
    public $count_reviews = 0;
   
    protected $listeners = ['reviewAdded' => 'updateReviewCount'];
  


    

    public function mount($prod_slug)
    {
        $this->prod_slug = $prod_slug;
        $this->getSingleProd();
        $this->countReviews();
       
    }

    //get specific product
    public function getSingleProd(){
      
        $this->product =   Product::with(['images','productCategories'])
            ->withCount('reviews')
            ->where([
                
                ['prod_slug', $this->prod_slug],
                ['is_visible_to_market',true]
            
            ])
            ->first();

        // $this->count_reviews = $this->product ? $this->product->reviews_count : 0;
    }

    public function updateReviewCount($productId)
    {
        if ($this->product && $this->product->id == $productId) {
            $this->countReviews(); // Refresh the product data
        }
    }


    // count how many reviews per product
    public function countReviews(){
        if($this->product){
         $this->count_reviews = ProductReview::where('product_id', $this->product->id)->count();
        }
    }
   
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.ecommerce.single-prod',[
            'product' => $this->product,
            'count_reviews' => $this->count_reviews
            
        ]
    
    );
    }
}
    