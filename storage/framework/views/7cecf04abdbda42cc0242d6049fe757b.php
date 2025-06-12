<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['post_index']));

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

foreach (array_filter((['post_index']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<div wire:key="home-post-<?php echo e($post_index->id); ?>">
<a class="group sm:flex rounded-xl" href="<?php echo e(route('page.blog.single', $post_index->post_slug)); ?>">
    <div class="flex-shrink-0 relative rounded-xl overflow-hidden h-[200px] sm:w-[250px] sm:h-[350px] w-full">
    <img class="absolute top-0 object-cover size-full start-0" src="<?php echo e(asset(Storage::url($post_index->post_image))); ?>" alt="<?php echo e($post_index->post_title); ?>">
    </div>

    <div class="grow">
    <div class="flex flex-col h-full p-4 sm:p-6">
        <div class="mb-3">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $post_index->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-full text-xs font-medium border border-amber-400 text-amber-400 dark:text-amber-500">
                    <?php echo e($category->category_name); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

        </div>
        <h3 class="text-lg font-semibold text-gray-800 sm:text-2xl group-hover:text-amber-600 dark:text-neutral-300 dark:group-hover:text-white">
            <?php echo e($post_index->post_title); ?>

        </h3>
        <p class="mt-2 text-gray-600 dark:text-neutral-400">
            <?php echo e($post_index->post_content_excerpt); ?>

        </p>

        <div class="mt-5 sm:mt-auto">
        <!-- Avatar -->
        <div class="flex items-center">
            <div class="flex-shrink-0">
            <img class="size-[46px] rounded-full" src="<?php echo e($post_index->author->profile_photo_url); ?>" alt="<?php echo e($post_index->author->name); ?>">
            </div>
            <div class="ms-2.5 sm:ms-4">
            <h4 class="font-semibold text-gray-800 dark:text-neutral-200">
                <?php echo e($post_index->author->name); ?>

            </h4>
            <p class="text-xs text-gray-500 dark:text-neutral-500">
               <?php echo e($post_index->created_at->diffForHumans()); ?>

            </p>
            </div>
        </div>
        <!-- End Avatar -->
        </div>
    </div>
    </div>
</a>
</div>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/components/blog-partials/blog-card.blade.php ENDPATH**/ ?>