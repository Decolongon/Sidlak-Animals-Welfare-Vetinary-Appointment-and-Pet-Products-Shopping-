<?php

namespace App\Filament\Resources\Ecommerce;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Enums\OrderStatusEnum;
use App\Models\Ecommerce\Order;
use App\Enums\PaymentStatusEnum;
// use Filament\Resources\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\Ecommerce\Product;
use App\Ecommerce\Models\OrderItem;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\Ecommerce\OrderResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Resources\Ecommerce\OrderResource\RelationManagers;
use App\Filament\Resources\Ecommerce\OrderResource\Widgets\OrderStatsOverview;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Ecommerce';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 3;

   //protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getModel()::count();

        return match (true) {
            $count  == 0 => 'danger',      // No orders: danger
            $count < 10  => 'warning',    //less than 10 orders: warning
            default      => 'success',     // Many orders: success
        };
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Order Details')
                ->schema([
                    Select::make('user_id')
                        ->label('Customer')
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->preload()
                        ->optionsLimit(5)
                        ->required()
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->name))
                        ->createOptionForm([
                            Section::make('User Details')
                                ->description('The user\'s name and email address.')
                                ->schema([
                                    TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                    TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->unique(ignoreRecord: true),

                                    TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    // ->required(fn (Page $livewire): bool => $livewire instanceof EditUser)
                                    // ->visible(fn (Page $livewire): bool => $livewire instanceof CreateUser)
                                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                                    

                                    TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->same('password')
                                    // ->required(fn (Page $livewire): bool => $livewire instanceof EditUser)
                                    // ->visible(fn (Page $livewire): bool => $livewire instanceof CreateUser)
                                    ->revealable(),
                                    
                            ])->columns(2),
                        ]),
                       
    
                    ToggleButtons::make('order_status')
                        ->options(OrderStatusEnum::class)
                        ->default(OrderStatusEnum::New)
                        ->dehydrated()
                        ->inline()
                        ->required()
                        ->label('Order Status'),
    
                    ToggleButtons::make('payment_status')
                        ->options(PaymentStatusEnum::class)
                        ->default(PaymentStatusEnum::Pending)
                        ->inline()
                        ->dehydrated()
                        ->required()
                        ->label('Payment Status'),

                
                   
                ])
                ->columns(1),

                Section::make('Addresses')
                ->schema([
                    Select::make('shipping_address_id')
                        ->label('Shipping Address')
                        ->relationship(name: 'shippingAddress', titleAttribute: 'full_address')
                        ->preload()
                        // ->required()
                        ->optionsLimit(5)
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('street')->required(),
                            TextInput::make('city')->required(),
                            TextInput::make('state')->required(),
                            TextInput::make('zip')->required(),
                            Hidden::make('address_type')->default('shipping'),
                        ]),

                    Select::make('billing_address_id')
                        ->label('Billing Address')
                        ->relationship(name: 'billingAddress', titleAttribute: 'full_address')
                        ->preload()
                        //->required()
                        ->dehydrated()
                        ->optionsLimit(5)
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('street')->required(),
                            TextInput::make('city')->required(),
                            TextInput::make('state')->required(),
                            TextInput::make('zip')->required(),
                            Hidden::make('address_type')->default('billing'),
                        ]),
                        
                    Toggle::make('is_billing_same_as_shipping')
                        ->label('Billing same as Shipping')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, $get) => 
                            $state ? $set('billing_address_id', $get('shipping_address_id')) : null
                        ),
                    
                    Select::make('shipping_method')
                        ->label('Shipping Method')
                        ->options([
                            'COD' => 'Cash on Delivery',
                            // 'gcash' => 'gcash',
                            // 'paymaya' => 'paymaya',
                            // 'card' => 'card',
                            // 'grab_pay' => 'grab_pay',
                        ])
                        ->default('COD')
                        ->required(),

                    Textarea::make('notes')
                    ->maxLength(1000)
                    ->rows(5)
                        ->label('Notes (optional)')
                   
                ])
                ->columns(2),       



    
            Section::make('Order Items')
                ->schema([

                   
                    Repeater::make('orderItems')
                        ->label('')
                        ->relationship('orderItems') 
                        ->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->relationship(name: 'product', titleAttribute: 'prod_name')
                                ->preload()
                                // ->multiple()
                                ->optionsLimit(5)
                                ->searchable()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                        $product = Product::find($state);
                                        $set('price', $product?->prod_price ?? 0);
                
                                        // Compute item shipping
                                        $shippingCost = ($product?->prod_requires_shipping) ? ($product->shipping_cost ?? 0) : 0;
                                        $set('shipping_cost', $shippingCost);
                
                                        $quantity = $get('quantity') ?? 1;
                                        $set('total', ($product?->prod_price ?? 0) * $quantity + $shippingCost);
                
                                        // Recalculate entire form totals
                                        $items = $get('../../orderItems') ?? [];
                                        $total = 0;
                                        $shipping = 0;
                                        foreach ($items as $item) {
                                            $total += $item['total'] ?? 0;
                                            $shipping += $item['shipping_cost'] ?? 0;
                                        }
                                        $set('../../shipping_price', $shipping);
                                        $set('../../total', $total);
                                })
                                // ->afterStateUpdated(fn ($state, $set) => 
                                //     $set('price', Product::find($state)?->prod_price)
                                   
                                //  )
                                  ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->prod_name)),
                            
                            TextInput::make('price')
                                ->numeric()
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->label('Price'),
                               
                           

                            TextInput::make('quantity')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required()
                                ->maxValue(fn ($get) => Product::find($get('product_id'))?->prod_quantity ?? 1)
                                // ->reactive()
                                // ->afterStateUpdated(fn ($state, $set, $get) => 
                                //     $set('total', ($get('price')) * ($state) + ($get('shipping_price')))
                                // )
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                        $price = $get('price') ?? 0;
                                        $shipping = $get('shipping_cost') ?? 0;
                                        $set('total', ($price * $state) + $shipping);
                
                                        $items = $get('../../orderItems') ?? [];
                                        $total = 0;
                                        $shipping = 0;
                                        foreach ($items as $item) {
                                            $total += $item['total'] ?? 0;
                                            $shipping += $item['shipping_cost'] ?? 0;
                                        }
                                        $set('../../shipping_price', $shipping);
                                        $set('../../total', $total);
                                })
                                ->afterStateHydrated(function ($state, $set, $get) {
                                    // Ensure quantity is in valid format based on unit
                                    $product = Product::find($get('product_id'));
                                    if ($product) {
                                        if ($product->prod_unit === 'pcs') {
                                            $set('quantity', intval($state)); // force whole number
                                        }
                                    }
                                })
                                ->rule(['integer'])
                                ->hint('Input whole number only.')
                                ->hintColor('warning')
                                ->label('Quantity'),
    
                          
    
                            // TextInput::make('total')
                            //     ->numeric()
                            //     ->required()
                            //     ->disabled()
                            //     ->dehydrated()
                            //     ->minValue(0)
                            //     ->label('Total'), 
                              
                                
                        ])->columns(2)
                          //->addable(false)
                          ->addActionLabel('Add Item') 
                          ->deletable(fn ($get) => count($get('orderItems')) > 1) 
                          ->reorderable()
                          ->collapsible()
                          ->reactive()
                          ->afterStateUpdated(function ($state, $set, $get) {
                                $items = $get('orderItems') ?? [];
                                $total = 0;
                                $shipping = 0;
                                foreach ($items as $item) {
                                    $total += $item['total'] ?? 0;
                                    $shipping += $item['shipping_cost'] ?? 0;
                                }
                                $set('shipping_price', $shipping);
                                $set('total', $total);
                        }),
                          
                        TextInput::make('shipping_price')
                        ->label('Shipping Cost')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                        
                        
                          TextInput::make('total')
                          ->numeric()
                          ->required()
                          ->disabled()
                          ->dehydrated()
                          ->minValue(0)
                        //   ->formatStateUsing(fn ($state) => number_format( $state, 2))
                          ->label('Total'), 

                 ]),
        ]);
    
            
           
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer Name')
                    ->sortable()
                    ->formatStateUsing(fn (string $state) : string => ucwords($state)) 
                    ->searchable(),

             

                TextColumn::make('orderItems.product.prod_name')
                    ->label('Product Ordered')
                    ->sortable()
                    ->limit(20)
                    ->searchable()
                    ->formatStateUsing(fn (string $state) : string => ucwords($state)),

              
                ImageColumn::make('images.url')
                    ->label('Product Image')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->limit(1)
                    ->getStateUsing(fn ($record) => 
                    $record->orderItems->first()?->product->images()
                        ->where('is_primary', true)
                        ->value('url')
                        
                    ?? 

                    $record->orderItems->first()?->product->images()
                        ->orderBy('created_at')
                        ->value('url')
                )->toggleable(isToggledHiddenByDefault: true),


                 TextColumn::make('orderItems')
                    ->label('Quantity')
                    ->formatStateUsing(function ($record) {
                        return $record->orderItems->map(function ($item) {
                            $qty = number_format($item->quantity,0) ?? 0;
                            $unit = $item->product->prod_unit ?? '';
                            $weight = $item->product->prod_weight ?? 0;

                            return $unit === 'pcs'
                                ? "{$qty}"
                                : "{$qty} × " . number_format($weight, 2) . $unit;
                        })->join(', ');
                    })
                    ->toggleable(isToggledHiddenByDefault: true),


                   TextColumn::make('notes')
                    ->limit(70)
                    ->html()
                    ->label('Notes')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('order_status')
                    ->label('Order Status')
                    ->formatStateUsing(fn ($state) => OrderStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown') // display label halin sa orderstatusenum
                    ->color(fn ($state) => OrderStatusEnum::tryFrom($state)?->getColor() ?? 'gray') 
                    ->icon(fn ($state) => OrderStatusEnum::tryFrom($state)?->getIcon() ?? null),
                
                    TextColumn::make('shipping_price')
                        ->label('Shipping Cost')
                        ->sortable()
                        ->formatStateUsing(function ($state) {
                            return $state == 0 ? 'Free Shipping' : number_format($state, 2);
                        })
                        ->badge()
                        ->color(fn ($record) => $record->shipping_price == 0 ? 'success' : 'gray'),

                 TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->formatStateUsing(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown') // display label halin sa paymentstatusenum
                    ->color(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getColor() ?? 'gray') 
                    ->icon(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getIcon() ?? null),

                TextColumn::make('shipping_method')
                    ->label('Shipping Method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (string $state) : string =>
                    ($state == 'gcash' || $state == 'paymaya' || $state == 'grab_pay' || $state == 'card') ? 'E-Wallet/' . strtoupper($state) :
                    strtoupper($state))
                    ->color(fn ($state) => $state == 'gcash' || $state == 'paymaya' || $state == 'grab_pay' || $state == 'card' ? 'success' : 'primary'),
                   
                 

                TextColumn::make('total')
                    ->label('Total')
                    ->sortable(),

               
            ])
           
            ->filters([
        
                    SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                   
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

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
                    
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                      BulkAction::make('update_order_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->requiresConfirmation() 
                    ->tooltip('Update Order and Payment status')
                    ->modalHeading('Confirm Status Update')
                    ->modalDescription( 'Are you sure you want to update the status of all selected orders ?')
                    ->color('warning') 
                    ->modalSubmitActionLabel('Confirm Update') 
                    ->modalWidth('2xl') // Change modal size: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
                        ->form([
                            ToggleButtons::make('order_status')
                                ->options(OrderStatusEnum::class)
                                ->dehydrated()
                                ->inline()
                                ->required()
                                ->label('Order Status'),

                            ToggleButtons::make('payment_status')
                                ->options(PaymentStatusEnum::class)
                                ->inline()
                                ->dehydrated()
                                ->required()
                                ->label('Payment Status'),
                        ])
                        ->action(function (array $data, $records) {
                         foreach($records as $record){
                             $record->update([
                                'order_status' => $data['order_status'],
                                'payment_status' => $data['payment_status'],
                            ]);
                        }
                         Notification::make()
                            ->title('Updates Successfuly')
                            ->success()
                            ->send();
                           
                        }),
                ]),
            ])->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('New Order')),
            ])
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No Orders are created');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public function formatShippingMethod($state)
    {
        // Check if the value is "e-wallet"
        if (strpos($state, 'e-wallet') !== false) {
            // Split the value into "e-wallet" and the specific wallet type (e.g., gcash, paymaya)
            $parts = explode('/', $state);
            // Return the formatted string
            return 'e-wallet/' . ucfirst($parts[1]);
        }

        // If it's not "e-wallet", just return the value (e.g., "COD")
        return $state;
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //        OrderStatsOverview::class
    //     ];
    // }



public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            InfoSection::make('Customer Details')
            ->icon('heroicon-o-user')
            ->iconColor('primary')
                ->schema([
                    TextEntry::make('user.name')->label('Name'),
                    TextEntry::make('user.email')->label('Email'),
                ])
                ->collapsible()
                ->columns(2),

            InfoSection::make('Order Details')
            ->icon('heroicon-o-shopping-cart')
            ->iconColor('primary')
                ->schema([
                    TextEntry::make('order_status')->label('Order Status')
                     ->color(fn ($state) => OrderStatusEnum::tryFrom($state)?->getColor())
                     ->icon(fn ($state) => OrderStatusEnum::tryFrom($state)?->getIcon())
                     ->formatStateUsing(fn ($state) => OrderStatusEnum::tryFrom($state)?->getLabel()),
                  
                    TextEntry::make('payment_status')->label('Payment Status')
                     ->color(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getColor())
                     ->icon(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getIcon())
                     ->formatStateUsing(fn ($state) => PaymentStatusEnum::tryFrom($state)?->getLabel()),

                    TextEntry::make('shipping_method')->label('Shipping Method')
                    ->badge(),
                    TextEntry::make('notes')->label('Notes')->html()->columnSpanFull(),
                ])
                ->collapsible()
                ->columns(2),

            InfoSection::make('Order Items')
            ->icon('heroicon-o-shopping-bag')
            ->iconColor('primary')
                ->schema([
                    RepeatableEntry::make('orderItems')
                        ->label('Items')
                        ->schema([
                            TextEntry::make('product.prod_name')->label('Product Name'),
                            TextEntry::make('price')->label('Price')->money('PHP')
                            ->badge()
                            ->formatStateUsing(fn ($state) => number_format($state, 2)),
                            TextEntry::make('quantity')->label('Quantity')
                            ->formatStateUsing(function ($state, $record) {
                                $qty = number_format($state, 0);
                                $unit = $record->product->prod_unit ?? '';
                                $weight = $record->product->prod_weight ?? 0;

                                return $unit === 'pcs'
                                    ? "{$qty}"
                                    : "{$qty} × " . number_format($weight, 2) . $unit;
                            })
                            ->badge(),
                           
                        ])
                        // ->collapsible()
                        ->columns(2)
                        ->columnSpanFull(),
                        
                    TextEntry::make('shipping_price')->label('Total Shipping Cost')->money('PHP')
                    ->badge()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                    TextEntry::make('total')->label('Grand Total')->money('PHP')
                     ->badge()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                ])
                ->columns(2)->collapsible(),
        ]);
}


}
