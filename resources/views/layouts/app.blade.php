<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') . ' - ' .'SDAS' }}</title>
        <link rel="icon" href="{{ asset('imgs/sdas-logo.png') }}" type="image/png">
       
     <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css'])
        @livewireStyles
      
    </head>
    <body class="font-sans antialiased dark:bg-neutral-800">
        @include('navigation-menu')
        <main>
            {{ $slot }}
        </main>

       @livewire('ecommerce.get-cart')
         <livewire:partials.footer />
       
        @stack('modals')

        @livewireScripts
        @vite(['resources/js/app.js'])
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
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
