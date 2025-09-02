<div > 
    {{-- Care about people's approval and you will be their prisoner. --}}

   

<x-button wire:click="addToCart" wire:loading.attr="disabled"  class="flex items-center justify-center gap-2 whitespace-nowrap text-xs sm:text-sm">
    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l1 5h13l1-5h2M5 8l1 9h12l1-9M9 20h.01M15 20h.01"></path>
    </svg>
    <span wire:loading.remove class="whitespace-nowrap ">Add to Cart</span>
    <span wire:loading.flex class="items-center">
        <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
       Adding..
    </span>
</x-button>



     

</div> 
