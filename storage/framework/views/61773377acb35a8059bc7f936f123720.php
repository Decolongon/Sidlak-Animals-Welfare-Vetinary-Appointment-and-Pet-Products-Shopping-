<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="<?php echo e(asset('imgs/sdas-logo.png')); ?>" type="image/png">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        

        <!-- Scripts -->
        
          <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
        <!-- Styles -->
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
[$__name, $__params] = $__split('partials.footer', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-715990072-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
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
            //  console.log('CSRF from blade:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
           $(document).ajaxSuccess(function(event, xhr, settings) {
                if (xhr.responseJSON && xhr.responseJSON.newToken) {
                    $('meta[name="csrf-token"]').attr('content', xhr.responseJSON.newToken);
                }
            });
          
        </script>
     
    </body>
</html>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/layouts/guest.blade.php ENDPATH**/ ?>