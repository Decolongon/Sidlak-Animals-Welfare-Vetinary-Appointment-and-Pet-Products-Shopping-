<div>   {{-- open --}}
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    <div class="max-w-lg mx-auto space-y-5">
        <!-- User Input Form -->
        <form wire:submit.prevent="submitReview" class="space-y-4 bg-white border p-4 rounded-2xl shadow-md dark:bg-neutral-900 dark:border-neutral-700">
            <h2 class="font-medium text-gray-800 dark:text-white">Leave a Review</h2>
    
            <!-- Rating -->
            <div class="flex justify-start items-center space-x-1">
                @for ($i = 1; $i <= 5; $i++)
                    <input id="rating-{{ $i }}" type="radio"  wire:click="$set('rating', {{ $i }})"
                        class="peer hidden" name="rating" value="{{ $i }}">
                        <label for="rating-{{ $i }}" class="cursor-pointer">
                            <svg class="size-6 transition-colors duration-200 
                                    @if($rating >= $i) text-yellow-400 @else text-gray-300 @endif" 
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                                viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
                            </svg>
                        </label>
                @endfor
               
            </div>
            <!-- End Rating -->
            @error('rating') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            

            <textarea wire:model.defer="review" placeholder="Write your review here..." class="w-full p-2 border rounded-md dark:bg-neutral-800 dark:text-white"></textarea>
            @error('review') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div x-data="{ uploading: false}" x-init="
            Livewire.hook('upload:start', () => uploading = true);
            Livewire.hook('upload:finish', () => uploading = false);
            Livewire.hook('upload:error', () => uploading = false);
            Livewire.on('imageUploaded', () => uploading = false); 
        ">
          {{-- <input type="file" wire:model="image_review" multiple @change="uploading = true"> --}}
          <label for="image_review" class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
            <svg class="mr-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" x2="12" y1="3" y2="15"></line>
            </svg>
            Choose Image(s)
            <input type="file" wire:model="image_review" multiple id="image_review" class="hidden" @change="uploading = true">
        </label>
          <div class="mt-2 text-black-500 dark:text-white"  x-show="uploading">Uploading...</div>
         {{-- wire:loading wire:target="image_review" --}}
            @error('image_review.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
              
            @if ($image_review)
                <div class="flex space x-25 overflow-x-auto mt-2 mb-2">
                @foreach ($image_review as $index => $image)
                    <div class="relative w-16 h-16">  
                        <!-- Remove Button -->
                        <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full px-2 py-1 text-xs shadow-lg"
                            wire:click="removeImage({{ $index }})">
                            X
                        </button>
        
                        <!-- Image Preview -->
                        <img class="rounded-md w-16 h-16 object-cover border" src="{{ $image->temporaryUrl() }}" alt="image">
                    </div>
                @endforeach
            </div>
            @endif
           
            <x-button type="submit" wire:target="image_review" class="mt-2"  wire:loading.attr="disabled">
                <span wire:loading.flex wire:target="submitReview"  class="items-center">
                    <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                   
                </span>
                Submit Review
            </x-button>
           
        </div>
        </form>
    </div>
    {{--end form}}


       
     
</div> 

{{-- close --}}