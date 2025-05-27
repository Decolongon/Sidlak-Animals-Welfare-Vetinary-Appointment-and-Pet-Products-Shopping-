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
  
    public $productId;
    public $relatedProduct;

    

    public function mount($prod_slug)
    {
        $this->prod_slug = $prod_slug;
        $this->getSingleProd();
        $this->getRelatedProduct();
        $this->countReviews();
       
    }

    //get specific product
    public function getSingleProd(){
      
        $this->product = Product::with(['images','productCategories',
             'productDiscounts' => function ($query) {
                $query->select('product_discounts.id', 'discount_name', 'start_at', 'end_at',)
                        ->where('start_at', '<=', now())
                        ->where('end_at', '>=', now());
                }
        ])
            ->withCount('reviews')
            ->where([
                
                ['prod_slug', $this->prod_slug],
                ['is_visible_to_market',true]
            
            ])
            ->first();

        if ($this->product) {
            $this->product->discounted_price = null;
            $this->product->discount_amount = null;
            $this->product->discount_label = null;

            $discount = $this->product->productDiscounts->first();

        if ($discount && $discount->pivot) {
            $type = $discount->pivot->discount_type;
            $value = floatval($discount->pivot->discounted_price);

            if ($type === 'fixed') {
                $this->product->discount_amount = $value;
                $this->product->discounted_price = $this->product->prod_price - $value;
                $this->product->discount_label = '₱' . number_format($value, 0) . ' off';
            } 
            if ($type === 'percent') {
                $discountValue = $this->product->prod_price * ($value / 100);
                $this->product->discount_amount = $discountValue;
                $this->product->discounted_price = $this->product->prod_price - $discountValue;
                $this->product->discount_label = number_format($value, 0) . '% off';
            }
        }
    }

        // $this->count_reviews = $this->product ? $this->product->reviews_count : 0;
    }

    //get Related Product
    public function getRelatedProduct(){
        $categoryIds = $this->product->productCategories->pluck('id')->toArray();
        $this->relatedProduct = Product::with(['images','productCategories',
            'productDiscounts' => function ($query) {
                $query->select('product_discounts.id', 'discount_name', 'start_at', 'end_at',)
                        ->where('start_at', '<=', now())
                        ->where('end_at', '>=', now());
                }
        ])
            ->whereHas('productCategories', function ($query) use ($categoryIds) {
            $query->whereIn('product_categories.id', $categoryIds);
        })
        ->where('is_visible_to_market',true)
        ->where('id', '!=', $this->product->id) // Exclude current product
        ->limit(4)
        ->get();

    // Compute discounts for each related product
    foreach ($this->relatedProduct as $product) {
        $product->discounted_price = null;
        $product->discount_amount = null;
        $product->discount_label = null;

        $discount = $product->productDiscounts->first();

        if (!$discount || !$discount->pivot) {
            continue;
        }

        $type = $discount->pivot->discount_type;
        $value = floatval($discount->pivot->discounted_price);

        if ($type === 'fixed') {
            $product->discount_amount = $value;
            $product->discounted_price = $product->prod_price - $value;
            $product->discount_label = '₱' . number_format($value, 0) . ' off';
        } 
        if ($type === 'percent') {
            $discountValue = $product->prod_price * ($value / 100);
            $product->discount_amount = $discountValue;
            $product->discounted_price = $product->prod_price - $discountValue;
            $product->discount_label = number_format($value, 0) . '% off';
        }
    }

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

    public function buyNow($productId){
        if(!Auth::check()){
            $this->alert('warning','', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'You must login first',
            ]);
            return redirect()->route('login');
        }
        
        session()->put('buy_now_product', $productId);
    
            return redirect()->route('checkout');
    }
   
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.ecommerce.single-prod',[
            'product' => $this->product,
            'count_reviews' => $this->count_reviews,
            'relatedProducts' => $this->relatedProduct
        ]
    
    );
    }
}
    