<?php
    $eventClickEnabled = $this->isEventClickEnabled();
    $eventDragEnabled = $this->isEventDragEnabled();
    $eventResizeEnabled = $this->isEventResizeEnabled();
    $noEventsClickEnabled = $this->isNoEventsClickEnabled();
    $dateClickEnabled = $this->isDateClickEnabled();
    $dateSelectEnabled = $this->isDateSelectEnabled();
    $datesSetEnabled = $this->isDatesSetEnabled();
    $viewDidMountEnabled = $this->isViewDidMountEnabled();
    $eventAllUpdatedEnabled = $this->isEventAllUpdatedEnabled();
    $onEventResizeStart = method_exists($this, 'onEventResizeStart');
    $onEventResizeStop = method_exists($this, 'onEventResizeStop');
    $hasDateClickContextMenu = !empty($this->getCachedDateClickContextMenuActions());
    $hasDateSelectContextMenu = !empty($this->getCachedDateSelectContextMenuActions());
    $hasEventClickContextMenu = !empty($this->getCachedEventClickContextMenuActions());
    $hasNoEventsClickContextMenu = !empty($this->getCachedNoEventsClickContextMenuActions());

    $dayHeaderFormatJs = $this->getDayHeaderFormatJs();
    $slotLabelFormatJs = $this->getSlotLabelFormatJs();
?>

<?php if (isset($component)) { $__componentOriginalb525200bfa976483b4eaa0b7685c6e24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    
    <style>
        .ec {
            --ec-event-bg-color: rgb(var(--primary-600));
            --ec-border-color: rgb(var(--gray-200));
            --ec-button-border-color: rgba(var(--gray-950), 0.1);
            --ec-button-bg-color: rgba(255, 255, 255, 1.0);
            --ec-button-active-bg-color: rgba(var(--gray-50), 1.0);
            --ec-button-active-border-color: var(--ec-button-border-color);

            & .ec-event.ec-preview {
                --ec-event-bg-color: rgb(var(--primary-400));
                z-index: 30;
            }

            & .ec-now-indicator {
                z-index:40;
            }
        }

        .dark .ec {
            --ec-event-bg-color: rgb(var(--primary-500));
            --ec-border-color: rgba(255, 255, 255, 0.10);
            --ec-button-border-color: rgba(var(--gray-600), 1.0);
            --ec-button-bg-color: rgba(255, 255, 255, 0.05);
            --ec-button-active-bg-color: rgba(255, 255, 255, 0.1);
            --ec-button-active-border-color: var(--ec-button-border-color);

            & .ec-event.ec-preview {
                --ec-event-bg-color: rgb(var(--primary-300));
            }
        }
    </style>
    
    <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['headerActions' => $this->getCachedHeaderActions(),'footerActions' => $this->getCachedFooterActions()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['header-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getCachedHeaderActions()),'footer-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getCachedFooterActions())]); ?>
         <?php $__env->slot('heading', null, []); ?> 
            <?php echo e($this->getHeading()); ?>

         <?php $__env->endSlot(); ?>

        <div
            wire:ignore
            x-ignore
            ax-load
            ax-load-src="<?php echo e(\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('calendar-widget', 'guava/calendar')); ?>"
            x-data="calendarWidget({
                view: <?php echo \Illuminate\Support\Js::from($this->getCalendarView())->toHtml() ?>,
                locale: <?php echo \Illuminate\Support\Js::from($this->getLocale())->toHtml() ?>,
                firstDay: <?php echo \Illuminate\Support\Js::from($this->getFirstDay())->toHtml() ?>,
                eventContent: <?php echo \Illuminate\Support\Js::from($this->getEventContentJs())->toHtml() ?>,
                resourceLabelContent: <?php echo \Illuminate\Support\Js::from($this->getResourceLabelContentJs())->toHtml() ?>,
                eventClickEnabled: <?php echo \Illuminate\Support\Js::from($eventClickEnabled)->toHtml() ?>,
                eventDragEnabled: <?php echo \Illuminate\Support\Js::from($eventDragEnabled)->toHtml() ?>,
                eventResizeEnabled: <?php echo \Illuminate\Support\Js::from($eventResizeEnabled)->toHtml() ?>,
                noEventsClickEnabled: <?php echo \Illuminate\Support\Js::from($noEventsClickEnabled)->toHtml() ?>,
                dateClickEnabled: <?php echo \Illuminate\Support\Js::from($dateClickEnabled)->toHtml() ?>,
                dateSelectEnabled: <?php echo \Illuminate\Support\Js::from($dateSelectEnabled)->toHtml() ?>,
                datesSetEnabled: <?php echo \Illuminate\Support\Js::from($datesSetEnabled)->toHtml() ?>,
                viewDidMountEnabled: <?php echo \Illuminate\Support\Js::from($viewDidMountEnabled)->toHtml() ?>,
                eventAllUpdatedEnabled: <?php echo \Illuminate\Support\Js::from($eventAllUpdatedEnabled)->toHtml() ?>,
                onEventResizeStart: <?php echo \Illuminate\Support\Js::from($onEventResizeStart)->toHtml() ?>,
                onEventResizeStop: <?php echo \Illuminate\Support\Js::from($onEventResizeStop)->toHtml() ?>,
                dayMaxEvents: <?php echo \Illuminate\Support\Js::from($this->dayMaxEvents())->toHtml() ?>,
                moreLinkContent: <?php echo \Illuminate\Support\Js::from($this->getMoreLinkContentJs())->toHtml() ?>,
                resources: <?php echo \Illuminate\Support\Js::from($this->getResourcesJs())->toHtml() ?>,
                hasDateClickContextMenu: <?php echo \Illuminate\Support\Js::from($hasDateClickContextMenu)->toHtml() ?>,
                hasDateSelectContextMenu: <?php echo \Illuminate\Support\Js::from($hasDateSelectContextMenu)->toHtml() ?>,
                hasEventClickContextMenu: <?php echo \Illuminate\Support\Js::from($hasEventClickContextMenu)->toHtml() ?>,
                hasNoEventsClickContextMenu: <?php echo \Illuminate\Support\Js::from($hasNoEventsClickContextMenu)->toHtml() ?>,
                options: <?php echo \Illuminate\Support\Js::from($this->getOptions())->toHtml() ?>,
                dayHeaderFormat: <?php echo e($dayHeaderFormatJs); ?>,
                slotLabelFormat: <?php echo e($slotLabelFormatJs); ?>,
            })"
        >
            <div id="calendar"></div>
            <?php if (isset($component)) { $__componentOriginal5ff0e703a07de8ebda296413721dea82 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ff0e703a07de8ebda296413721dea82 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'guava-calendar::components.context-menu','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guava-calendar::context-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ff0e703a07de8ebda296413721dea82)): ?>
<?php $attributes = $__attributesOriginal5ff0e703a07de8ebda296413721dea82; ?>
<?php unset($__attributesOriginal5ff0e703a07de8ebda296413721dea82); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ff0e703a07de8ebda296413721dea82)): ?>
<?php $component = $__componentOriginal5ff0e703a07de8ebda296413721dea82; ?>
<?php unset($__componentOriginal5ff0e703a07de8ebda296413721dea82); ?>
<?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal028e05680f6c5b1e293abd7fbe5f9758 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-actions::components.modals','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-actions::modals'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758)): ?>
<?php $attributes = $__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758; ?>
<?php unset($__attributesOriginal028e05680f6c5b1e293abd7fbe5f9758); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal028e05680f6c5b1e293abd7fbe5f9758)): ?>
<?php $component = $__componentOriginal028e05680f6c5b1e293abd7fbe5f9758; ?>
<?php unset($__componentOriginal028e05680f6c5b1e293abd7fbe5f9758); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $attributes = $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $component = $__componentOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\sidlak-vet-appointment-and-pet-shop\vendor\guava\calendar\src\/../resources/views/widgets/calendar.blade.php ENDPATH**/ ?>