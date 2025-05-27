<?php

namespace App\Filament\Resources\Ecommerce\OrderResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\Ecommerce\OrderResource;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var Order */
        $record = $this->getRecord();

        return ucwords($record->user->name);
    }

     protected function getActions(): array
    {
        return [
              Action::make('back_to_list')
                ->label('Back to Orders')
                ->icon('heroicon-o-arrow-left')
                ->color('primary')
                ->url(fn () => static::getResource()::getUrl()), 
                // ->outlined(),

        Actions\ActionGroup::make([
            Actions\EditAction::make(),

             Action::make('update_order_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->requiresConfirmation() 
                    ->tooltip('Update Order and Payment status')
                    ->modalHeading(fn ($record) => 'Confirm Status Update')
                    ->modalDescription(fn ($record) => 'Are you sure you want to update the status of ' . $record->user->name . '?')
                    ->color('warning') 
                    ->modalSubmitActionLabel('Confirm Update') 
                    ->modalWidth('2xl') // Change modal size: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
                        ->form([
                            ToggleButtons::make('order_status')
                                ->options(OrderStatusEnum::class)
                                // ->default(OrderStatusEnum::New)
                                ->default(fn ($record) => $record->order_status)
                                ->dehydrated()
                                ->inline()
                                ->required()
                                ->label('Order Status'),

                            ToggleButtons::make('payment_status')
                                ->options(PaymentStatusEnum::class)
                                // ->default(PaymentStatusEnum::Pending)
                                ->default(fn ($record) => $record->payment_status)
                                ->inline()
                                ->dehydrated()
                                ->required()
                                ->label('Payment Status'),
                        ])
                        ->action(function (array $data, $record) {
                            $record->update([
                                'order_status' => $data['order_status'],
                                'payment_status' => $data['payment_status'],
                            ]);
                             Notification::make()
                            ->title('Updates Successfuly')
                            ->success()
                            ->send();
                        }),
        ])

            
           
        ];
    }
}
