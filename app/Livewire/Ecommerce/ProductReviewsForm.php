<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Ecommerce\ProductReview;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class ProductReviewsForm extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $review;
    public $rating = null;
    public $image_review = [];

    #[Locked]
    public $product_id;
    
    public $prod_review;

    
    protected $rules = [
        'review' => 'max:10000',
        // 'image_review' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'image_review.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'rating' => 'required|numeric|min:1|max:5',
    ];

    public function mount($product_id)
    {
        //$this->submitReview();
        // $this->getProdReviews();
        $this->product_id = $product_id;

    }

    // #[On('showModal')]
    // public function getModal($state)
    // {
    //     $this->showModal = $state;
    // }

    //sanitize input
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    //only authenticated user ang maka submit reviews
    public function submitReview()
    {
       
        if (!Auth::check()) {
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
                'text' => 'You must login first'

            ]);
            return redirect()->route('login');
        }

        $this->validate();

        //dd($this->image_review);
        $img_path = [];

        if (!empty($this->image_review)) {
            foreach ($this->image_review as $img) {


                $path = $img->store('', 'public');
                $img_path[] = str_replace('public/', '', $path); // Remove "public/" from the path



            }
        }
        //$this->rating !== '' ? (int) $this->rating : null,
        $data = $this->sanitizeInput([
            'review' => $this->review,
            'rating' => $this->rating,
            'product_id' => $this->product_id,
            'image_review' => is_array($img_path) ? $img_path : json_decode($img_path, true),
            'user_id' => Auth::id(),
        ]);


        ProductReview::create($data);

        //delete image review sa temporary folder tapos insert sa database and sa public storage 
        if ($this->image_review) {
            foreach ($this->image_review as $img) {
                $img->delete();
            }
        }

        $this->alert('success', '', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Review submitted successfully!'

        ]);
        $this->reset(['review', 'rating', 'image_review']);
        $this->dispatch('reviewAdded', $this->product_id);

        // session()->flash('success', 'Review submitted successfully!');
    }

    // public function getProdReviews(){
    //    $this->prod_review = ProductReview::where('product_id', $this->product_id)->get(['id', 'product_id', 'user_id', 'review', 'rating']);
    // }

    public function cancelReview()
    {


        $this->reset(['review', 'rating', 'image_review']);
    }


    public function updatedImageReview()
    {

        if (!empty($this->image_review)) {
            $this->dispatch('imageUploaded'); // Send event to Alpine
        }
    }

    public function removeImage($index)
    {

        if (isset($this->image_review[$index])) {
            $this->image_review[$index]->delete();
            unset($this->image_review[$index]);  // Remove image from array
            $this->image_review = array_values($this->image_review); // Reindex array
        }
    }

    public function cancelUpload()
    {

        if (isset($this->image_review)) {
            foreach ($this->image_review as $img) {
                $img->delete();
            }
        }
        return back();
    }

    #[Layout('layouts.app')]
    #[Title('Product Reviews Form')]
    public function render()
    {
        return view('livewire.ecommerce.product-reviews-form', [
            // 'prod_reviews' => $this->prod_review
        ]);
    }
}
