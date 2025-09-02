<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="<?php echo e(config('app.name')); ?>">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e(config('app.name') . ' - ' .'SDAS'); ?></title>
        <link rel="icon" href="<?php echo e(asset('imgs/sdas-logo.png')); ?>" type="image/png">
       
     <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
        <!-- Fonts -->
        
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

      
    </head>
    <body class="font-sans antialiased dark:bg-neutral-800">
        <?php echo $__env->make('navigation-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <main>
            <?php echo e($slot); ?>

        </main>

       <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('ecommerce.get-cart');

$__html = app('livewire')->mount($__name, $__params, 'lw-3263394333-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
         <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('partials.footer', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3263394333-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
       
        <?php echo $__env->yieldPushContent('modals'); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php if (isset($component)) { $__componentOriginal8344cca362e924d63cb0780eb5ae3ae6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8344cca362e924d63cb0780eb5ae3ae6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'livewire-alert::components.scripts','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('livewire-alert::scripts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8344cca362e924d63cb0780eb5ae3ae6)): ?>
<?php $attributes = $__attributesOriginal8344cca362e924d63cb0780eb5ae3ae6; ?>
<?php unset($__attributesOriginal8344cca362e924d63cb0780eb5ae3ae6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8344cca362e924d63cb0780eb5ae3ae6)): ?>
<?php $component = $__componentOriginal8344cca362e924d63cb0780eb5ae3ae6; ?>
<?php unset($__componentOriginal8344cca362e924d63cb0780eb5ae3ae6); ?>
<?php endif; ?>
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script>
            if (typeof attrs === 'undefined') {
                let attrs = [
                    'snapshot',
                    'effects',
                ];

                function snapKill() {
                    document.querySelectorAll('div').forEach(function(element) {
                        for (let i in attrs) {
                            if (element.getAttribute(`wire:${attrs[i]}`) !== null) {
                                element.removeAttribute(`wire:${attrs[i]}`);
                            }
                        }
                    });
                }

                window.addEventListener('load', (ev) => {
                    snapKill();
                });
            }

        //pop up confirmation for checkout btn nga outofstock
           Livewire.on('outOfStockDetected', () => {
                Swal.fire({
                    title: 'Some items are out of stock',
                    text: 'Do you want to remove them from the cart and proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('removeOutOfStockConfirmed');
                    }
                });
            });



        </script>

     
      
    </body>
</html>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/layouts/app.blade.php ENDPATH**/ ?>