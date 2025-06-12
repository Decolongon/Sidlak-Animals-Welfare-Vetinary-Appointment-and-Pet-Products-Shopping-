<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'maxWidth']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['id', 'maxWidth']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$id = $id ?? md5($attributes->wire('model'));

$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    'xxl' => 'sm:max-w-[70rem]',
][$maxWidth ?? '2xl'];
?>

<div
    x-data="{ show: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>.live }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="<?php echo e($id); ?>"
    class="fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none px-4 py-6 jetstream-modal sm:px-0 hs-overlay"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        class="fixed inset-0 transition-all transform bg-gray-500 bg-opacity-0"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0.1"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal Content -->
    <div
        x-show="show"
        class="relative flex flex-col bg-white shadow-lg pointer-events-auto rounded-xl dark:bg-neutral-950 transform transition-all sm:w-full <?php echo e($maxWidth); ?> sm:mx-auto"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0.1 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <!-- Header -->
        <div class="relative overflow-hidden text-center bg-amber-500 min-h-20 rounded-t-xl">
            <!-- Close Button -->
            <div class="absolute top-2 end-2">
                <button
                    type="button"
                    class="flex items-center justify-center text-sm font-semibold border border-transparent rounded-full size-7 text-white/70 hover:bg-white/10 focus:outline-none focus:bg-white/10"
                    aria-label="Close"
                    x-on:click="show = false"
                >
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            
        </div>

        <!-- Body -->
        <div class="relative z-10 -mt-12">
            <!-- Icon -->
            <span class="mx-auto flex justify-center items-center size-[62px] rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                <img src="<?php echo e(asset('imgs/sdas-logo.png')); ?>" alt="" width="120px" height="120px">
            </span>
            <!-- End Icon -->
        </div>

        <!-- Slot -->
        <div class="p-6">
            <?php echo e($slot); ?>

        </div>
    </div>
</div>


<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/components/modal.blade.php ENDPATH**/ ?>