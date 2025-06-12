<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['dog']));

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

foreach (array_filter((['dog']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div wire:key="home-dog-<?php echo e($dog->id . '-' . $dog->dog_slug); ?>" class="bg-center bg-no-repeat">
    <a href="<?php echo e(route('page.dog.single', $dog->dog_slug)); ?>">
        <img class="rounded-xl w-auto h-auto max-h-[300px] sm:max-h-[400px] md:max-h-[500px] lg:max-h-[300px] xl:max-h-[400px]" src="<?php echo e(asset(Storage::url($dog->dog_image[0]['dog_image']))); ?>" alt="<?php echo e($dog->dog_name); ?>">
    </a>
</div>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/components/home-page-partials/dog-card.blade.php ENDPATH**/ ?>