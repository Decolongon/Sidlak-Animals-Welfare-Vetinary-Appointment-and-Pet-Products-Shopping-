<div id="hs-offcanvas-custom-backdrop-color" class="hs-overlay hs-overlay-open:translate-x-0 hs-overlay-backdrop-open:bg-black/50 dark:hs-overlay-backdrop-open:bg-black/30 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-xs w-full z-[80] bg-white border-s dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="hs-offcanvas-custom-backdrop-color-label" wire:ignore.self>
  <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
      <h3 id="hs-offcanvas-custom-backdrop-color-label" class="font-bold text-gray-800 dark:text-white">
          Shopping Cart
      </h3>
      <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#hs-offcanvas-custom-backdrop-color">
          <span class="sr-only">Close</span>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6 6 18"></path>
              <path d="m6 6 12 12"></path>
          </svg>
      </button>
  </div>
  <div class="p-4">
      
       <!-- Select All Checkbox -->
       <!--[if BLOCK]><![endif]--><?php if(count($carts) > 0): ?>
        <div class="flex items-center mb-4">
          <input type="checkbox" name="selectAll" wire:model="selectAll" wire:click="toggleSelectAll" class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200">
          <p class="text-gray-700 dark:text-white text-sm font-medium">Select All</p>
        </div>
       <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
     

      
        <div class="flex flex-col gap-4 max-h-[70vh] overflow-y-auto pr-2 pb-28">
          <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="p-4 border rounded-lg shadow dark:border-neutral-700 dark:shadow-gray-900">
              <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                      <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring focus:ring-indigo-200" name="selectedItems" wire:model="selectedItems" value="<?php echo e($cart->id); ?>">
                      <img src="<?php echo e(asset(Storage::url($cart->product->images[0]->url))); ?>" alt="<?php echo e($cart->product->prod_slug); ?>" class="w-[40px] h-[40px] object-cover rounded-full">
                      <div>
                          <div class="font-medium text-gray-800 dark:text-neutral-200"><?php echo e(ucwords($cart->product->prod_name)); ?></div>
                          <div class="text-sm text-gray-600 dark:text-neutral-400">
                                
                            
                                ₱<?php echo e(number_format($cart->quantity * $cart->product->prod_price, 2)); ?>

                            </div>
                      </div>
                  </div>
              </div>

              <div class="mt-3 flex items-center justify-end gap-2">
              
                
                  <button type="button"  wire:click="decreaseQuantity('<?php echo e($cart->id); ?>')"  wire:loading.attr="disabled"  wire:target="decreaseQuantity('<?php echo e($cart->id); ?>')"
                      class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                      -
                  </button>

                  <span class="text-lg font-semibold text-gray-900 dark:text-white">
                      <?php echo e(number_format($cart->quantity,0)); ?>

                    
                  </span>

                  <button type="button" wire:click="increaseQuantity('<?php echo e($cart->id); ?>')" wire:loading.attr="disabled"  wire:target="increaseQuantity('<?php echo e($cart->id); ?>')"
                      class="px-3 py-1 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                      +
                  </button>
              </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="text-center py-10 text-gray-500 dark:text-neutral-400 text-lg">
              Cart is Empty.
          </div>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
      </div>

    <!--[if BLOCK]><![endif]--><?php if(count($carts) > 0): ?>
        <div class="sticky bottom-8 left-0 bg-white dark:bg-neutral-800 p-4 border-t dark:border-neutral-700 z-10">
            <div class="mt-4 flex gap-3">
                <button wire:click="removeSelected" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Remove Item
                </button>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['wire:click' => 'checkout','class' => 'px-3 py-1.5 text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'checkout','class' => 'px-3 py-1.5 text-sm']); ?>
                    Checkout
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
  </div>


</div>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\resources\views/livewire/ecommerce/get-cart.blade.php ENDPATH**/ ?>