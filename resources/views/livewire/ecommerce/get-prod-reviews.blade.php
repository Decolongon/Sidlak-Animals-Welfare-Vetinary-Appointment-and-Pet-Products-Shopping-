<div>   {{-- Open --}}
  
    <div class="p-4">
        <h2 class="text-lg font-semibold mb-4"></h2>
    
        @forelse($prod_reviews as $review)
            <div class="mb-4 p-4 border rounded-lg bg-white dark:bg-neutral-900">
                <div class="flex items-center gap-x-3">
                    <img class="w-10 h-10 rounded-full" src="{{ $review->user->profile_photo_url ?? 'https://via.placeholder.com/40' }}" alt="User Avatar">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $review->user->name ?? 'Anonymous' }}</p>
                        <div class="flex">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="size-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                                     viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
                                </svg>
                            @endfor
                        </div>
                        {{-- display san u gin post rev --}}
                        <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <p class="mt-2 text-gray-800 dark:text-white">{{ $review->review }}</p>
                @if (!empty($review->image_review))
                <div class="flex flex-row space-x-4">
                    @foreach($review->image_review as $img)
                        <img src="{{ asset(Storage::url($img)) }}" alt="Review Image" class="w-32 h-32 object-cover rounded-lg">
                    @endforeach
                </div>
                   
                @endif
            </div>
        @empty
            <p class="text-gray-500">No reviews yet.</p>
        @endforelse
    </div>


</div>   {{-- close --}}
