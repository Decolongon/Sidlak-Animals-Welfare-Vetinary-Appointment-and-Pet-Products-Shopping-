<div>
    <div class="max-w-lg mx-auto space-y-5">
        <!-- User Input Form -->
        <form wire:submit="submitReview" class="space-y-4 bg-white border p-4 rounded-2xl shadow-md dark:bg-neutral-900 dark:border-neutral-700">
            <h2 class="font-medium text-gray-800 dark:text-white">Leave a Review</h2>
    
            <!-- Rating -->
            <div class="flex justify-start items-center space-x-1">
                @for ($i = 1; $i <= 5; $i++)
                    <input id="rating-{{ $i }}" type="radio" wire:click="$set('rating', {{ $i }})"
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
            
            <textarea wire:model.defer="review" name="review" id="review" placeholder="Write your review here..." class="w-full p-2 border rounded-md dark:bg-neutral-800 dark:text-white"></textarea>
            @error('review') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

             <div x-data="{ uploading: false, isDragging: false }" 
                x-init="
                    Livewire.hook('upload:start', () => uploading = true);
                    Livewire.hook('upload:finish', () => uploading = false);
                    Livewire.hook('upload:error', () => uploading = false);
                    Livewire.on('imageUploaded', () => uploading = false);
                "
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false"
                @drop.prevent="isDragging = false; $wire.uploadMultiple('image_review', $event.dataTransfer.files, (success) => { uploading = false })">
                
                <!-- The rest of your code remains exactly the same -->
                <!-- Drag and Drop Area -->
                <div class="border-2 border-dashed rounded-xl p-6 text-center transition-all duration-300 mb-4 group"
                    :class="isDragging ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-400'">
                    
                    <!-- Visual upload icon that animates on hover -->
                    <div class="flex justify-center mb-3">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 group-hover:scale-110 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                    </div>

                    <!-- Enhanced file input button -->
                    <label for="image_review" class="cursor-pointer inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:from-blue-500 hover:to-blue-400">
                        <svg class="mr-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" x2="12" y1="3" y2="15"></line>
                        </svg>
                        <span class="font-medium">Browse Files</span>
                        <input type="file" wire:model="image_review" multiple id="image_review" class="hidden" @change="uploading = true">
                    </label>

                    <!-- Supporting text with better typography -->
                    <div class="mt-3 space-y-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">or drag & drop files here</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPEG, GIF, JPG (Max 2MB each)</p>
                    </div>

                    <!-- Active drag state indicator (shown only during drag) -->
                    <div x-show="isDragging" class="absolute inset-0 bg-blue-50/50 dark:bg-blue-900/10 rounded-xl flex items-center justify-center border-2 border-blue-500 border-dashed">
                        <div class="text-center p-4">
                            <svg class="mx-auto h-10 w-10 text-blue-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mt-2 font-medium text-blue-600 dark:text-blue-300">Drop to upload</p>
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-blue-500 dark:text-blue-400" x-show="uploading">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </div>
                </div>

                @error('image_review.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                @if ($image_review)
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mt-2 mb-2">
                        @foreach ($image_review as $index => $image)
                            <div class="relative group">
                                <div class="relative overflow-hidden rounded-md shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <img class="w-full h-20 object-cover" src="{{ $image->temporaryUrl() }}" alt="Preview">
                                    
                                    <button type="button" 
                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow hover:bg-red-600 text-xs"
                                            wire:click="removeImage({{ $index }})">
                                        X
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
           
            <x-button type="submit" wire:target="image_review" class="mt-2" wire:loading.attr="disabled">
                <span wire:loading.flex wire:target="submitReview" class="items-center">
                    <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                Submit Review
            </x-button>
        </form>
    </div>
</div>