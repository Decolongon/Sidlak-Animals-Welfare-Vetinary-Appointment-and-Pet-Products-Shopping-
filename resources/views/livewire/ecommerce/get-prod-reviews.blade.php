<div> {{-- Open --}}

    <div class="p-4">
        <h2 class="text-lg font-semibold mb-4"></h2>
        @forelse($this->getProdReviews as $review)
            <div class="mb-4 p-4 border rounded-lg bg-white dark:bg-neutral-900" wire:key="reviews{{ $review->id }}">

                <div class="flex items-center gap-x-3">
                    <img class="w-10 h-10 rounded-full"
                        src="{{ $review->user->profile_photo_url ?? 'https://via.placeholder.com/40' }}"
                        alt="User Avatar">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $review->user->name ?? 'Anonymous' }}</p>
                        <div class="flex">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="size-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z">
                                    </path>
                                </svg>
                            @endfor
                        </div>
                        {{-- display san u gin post rev --}}
                        <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                {{-- <p class="mt-2 text-gray-800 dark:text-white">{{ $review->review }}</p> --}}
                <div x-data="{
                    expanded: false,
                    fullText: @js($review->review),
                    shortText: @js(Str::limit($review->review, 100, ''))
                }" class="mt-2 text-gray-800 dark:text-white">

                    <p>
                        <span x-text="expanded ? fullText : shortText"></span>
                        <template x-if="fullText.length > 100">
                            <button class="text-blue-500 underline ml-2" @click="expanded = !expanded">
                                <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                            </button>
                        </template>
                    </p>
                </div>

                @if (!empty($review->image_review))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($review->image_review as $img)
                            <img src="{{ asset(Storage::url($img)) }}" alt="Review Image"
                                class="w-24 h-24 object-cover rounded-md md:w-26 md:h-26 lg:w-30 lg:h-30" />
                        @endforeach
                    </div>
                @endif
            </div>
        @empty

            <p class="text-gray-500">No reviews yet.</p>
        @endforelse
    </div>

   {{-- @if($prod_reviews->count() > 0)
        <div class="flex justify-center mt-6">
            @if ($showAll == false)
                <button 
                    wire:click="showAllReviews"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Show All Reviews
                </button>
            @else
                <button 
                    wire:click="getProdReviews"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-200 flex items-center gap-2 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-white"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Show Less
                </button>
            @endif
        </div>
    @endif --}}




</div> {{-- close --}}
