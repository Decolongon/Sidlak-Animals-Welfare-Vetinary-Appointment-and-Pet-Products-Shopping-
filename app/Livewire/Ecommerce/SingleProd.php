<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

use Livewire\Attributes\Locked;
use App\Models\Ecommerce\Product;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProductDiscountHelper;
use App\Models\Ecommerce\ProductReview;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Computed;

class SingleProd extends Component
{
    use LivewireAlert;

    public $product;
    #[Locked]
    private $prod_slug;

    public $activeTab = 'description';
    public $count_reviews = 0;

   // protected $listeners = ['reviewAdded' => 'updateReviewCount'];

    #[Locked]
    public $productId;


    public $primary_image;
    public $relatedProduct;


    public function mount($prod_slug)
    {
        $this->prod_slug = $prod_slug;
        $this->getSingleProd();
        $this->countReviews();
       // $this->getRelatedProduct();
    }

    //get specific product
    public function getSingleProd()
    {

        $this->product = Product::with([
            'images',
            'productCategories',
            'productDiscounts' => function ($query) {
                $query->ActiveProdDiscount(); // scope ara sa productDiscount model
            }
        ])
            ->withCount('reviews')
            ->where([

                ['prod_slug', $this->prod_slug],
                ['is_visible_to_market', true],

            ])
            ->first();

        $this->primary_image = $this->product->images->where('is_primary', true)->first() ?? $this->product->images->first();

        //if product my discount compute the discount price
        // if ($this->product) {
        //     $this->product->calculateDiscountedPrice();//ari sa product model; 
        // }
        $this->prodDiscountRender([$this->product]); // Compute discounts for the current product ($this->product);
        //$this->count_reviews = $this->product ? $this->product->reviews->count() : 0;
    }

    //get Related Product
    #[Computed()]
    public function getRelatedProduct()
    {
        $categoryIds = $this->product->productCategories->pluck('id')->toArray();
        $relatedProducts = Product::with([
            'images',
            'productCategories',
            'productDiscounts' => function ($query) {
                $query->ActiveProdDiscount(); // scope ara sa productDiscount model
            }
        ])
            ->whereHas('productCategories', function ($query) use ($categoryIds) {
                $query->whereIn('product_categories.id', $categoryIds);
            })
            ->where('is_visible_to_market', true)
            ->where('id', '!=', $this->product->id) // Exclude current product
            ->limit(4)
            ->get();

        // Compute discounts for each related product
        $this->prodDiscountRender($relatedProducts);

        $relatedProducts->each(function ($product) {
            $product->primary_image = $product->images->where('is_primary', true)->first()
                ?? $product->images->first();
        });

        return $relatedProducts;
    }


    /**
     * @param [type] $prod
     * @return void
     * calculate product discount
     */
    protected function prodDiscountRender($prod)
    {
        app(ProductDiscountHelper::class)->calculateDiscountedPrice($prod);
    }

    // public function updateReviewCount($productId)
    // {
    //     if ($this->product && $this->product->id == $productId) {
    //         $this->countReviews(); // Refresh the product data
    //     }
    // }


    // count how many reviews per product
    #[On('reviewAdded')]
    public function countReviews()
    {
        if ($this->product) {
            $this->count_reviews = ProductReview::where('product_id', $this->product->id)
                ->count();
        }
    }


    // #[On('buyNowSize')]
    public function buyNow($productId)
    {
        if (!Auth::check()) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'You must login first',
            ]);
            return redirect()->route('login');
        }

        // $buyNowItems = session()->get('buy_now_product', []);

        // // // Check if this product is already in buy now items
        // if (!in_array($productId, $buyNowItems)) {
        //     $buyNowItems[] = $productId;
        //     session()->put('buy_now_product', $buyNowItems);
        // }

        // $existingCheckoutItems = session()->get('selected_checkout_items', []);

        // if (!empty($existingCheckoutItems)) {
        //     $this->alert('warning', '', [
        //         'position' => 'top-end',
        //         'timer' => 5000,
        //         'showCloseButton' => true,
        //         'toast' => true,
        //         'text' => 'You have pending checkout items.',
        //     ]);
        //     return;
        // }
       // session()->put('buy_now_mode', true);
        session()->put('buy_now_product', $productId);
        session()->forget('selected_checkout_items');

        //return redirect()->route('checkout');
        return $this->redirect(route('checkout'));
    }

    #[Layout('layouts.app')]
    public function render()
    {

        if ($this->product) {
            $this->prodDiscountRender([$this->product]);
        }

        if ($this->relatedProduct) {
            // $this->prodDiscountRender($this->relatedProduct);
            $this->getRelatedProduct();
        }

        return view(
            'livewire.ecommerce.single-prod',
            [
                'product' => $this->product,
                'count_reviews' => $this->count_reviews,
               // 'relatedProducts' => $this->relatedProduct
            ]

        );
    }
}
