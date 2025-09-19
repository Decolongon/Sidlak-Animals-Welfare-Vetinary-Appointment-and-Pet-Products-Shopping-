<?php

namespace App\Filament\Resources\Ecommerce;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\OrderStatusEnum;
// use Filament\Resources\Pages\Page;
use App\Models\Ecommerce\Order;
use App\Enums\PaymentStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\Ecommerce\Product;
use App\Ecommerce\Models\OrderItem;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;

use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\Ecommerce\OrderResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use Filament\Infolists\Components\Section as InfoSection;
use App\Filament\Resources\Ecommerce\OrderResource\RelationManagers;
use App\Filament\Resources\Ecommerce\OrderResource\Widgets\OrderStatsOverview;
use Filament\Tables\Actions\Action as TableAction;

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
                            ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->name))
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
                                            ->dehydrateStateUsing(fn($state) => bcrypt($state)),


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
                            ->default(OrderStatusEnum::Pending)
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

                // Section::make('Addresses')
                //     ->schema([
                //         Select::make('shipping_address_id')
                //             ->label('Shipping Address')
                //             ->relationship(name: 'shippingAddress', titleAttribute: 'full_address')
                //             ->preload()
                //             // ->required()
                //             ->optionsLimit(5)
                //             ->searchable()
                //             ->createOptionForm([
                //                 TextInput::make('street')->required(),
                //                 TextInput::make('city')->required(),
                //                 TextInput::make('state')->required(),
                //                 TextInput::make('zip')->required(),
                //                 Hidden::make('address_type')->default('shipping'),
                //             ]),

                //         Select::make('billing_address_id')
                //             ->label('Billing Address')
                //             ->relationship(name: 'billingAddress', titleAttribute: 'full_address')
                //             ->preload()
                //             //->required()
                //             ->dehydrated()
                //             ->optionsLimit(5)
                //             ->searchable()
                //             ->createOptionForm([
                //                 TextInput::make('street')->required(),
                //                 TextInput::make('city')->required(),
                //                 TextInput::make('state')->required(),
                //                 TextInput::make('zip')->required(),
                //                 Hidden::make('address_type')->default('billing'),
                //             ]),

                //         Toggle::make('is_billing_same_as_shipping')
                //             ->label('Billing same as Shipping')
                //             ->reactive()
                //             ->afterStateUpdated(
                //                 fn($state, $set, $get) =>
                //                 $state ? $set('billing_address_id', $get('shipping_address_id')) : null
                //             ),

                //         Select::make('shipping_method')
                //             ->label('Shipping Method')
                //             ->options([
                //                 'COD' => 'Cash on Delivery',
                //                 // 'gcash' => 'gcash',
                //                 // 'paymaya' => 'paymaya',
                //                 // 'card' => 'card',
                //                 // 'grab_pay' => 'grab_pay',
                //             ])
                //             ->default('COD')
                //             ->required(),

                //         Textarea::make('notes')
                //             ->maxLength(1000)
                //             ->rows(5)
                //             ->label('Notes (optional)')

                //     ])
                //     ->columns(2),




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
                                    ->getOptionLabelFromRecordUsing(fn($record) => ucwords($record->prod_name)),

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
                                    ->maxValue(fn($get) => Product::find($get('product_id'))?->prod_quantity ?? 1)
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
                            ->deletable(fn($get) => count($get('orderItems')) > 1)
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
            ->groups([
                Group::make('created_at')
                    ->label('Ordered at')
                    ->collapsible()
                    ->date(),
            ])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer Name')
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => ucwords($state))
                    ->searchable()
                    ->weight('medium')
                    ->color('gray-700'),

                TextColumn::make('user.address.complete_address')
                    ->label('Address')
                    ->html()
                    ->limit(30)
                    ->formatStateUsing(fn(string $state): string => ucwords($state)),

                TextColumn::make('billingAddress.bil_complete_address')
                    ->label('Billing Address')
                    ->limit(30)
                    ->formatStateUsing(fn(string $state): string => ucwords($state)),

                //->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('order_num')
                    ->label('Tracking #')
                    ->badge()
                    ->searchable()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Tracking number copied!'),

                TextColumn::make('orderItems.product.prod_name')
                    ->label('Product Ordered')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $items = $record->orderItems;

                        $html = $items->map(function ($item) {
                            return "<div class='min-h-[50px] flex items-center text-sm text-gray-700'>" .
                                ucwords(Str::limit($item->product->prod_name, 20)) .
                                "</div>";
                        })->join('');

                        return $html;
                    })
                    ->html()
                    ->searchable(),

                TextColumn::make('images.url')
                    ->label('Product Image')
                    ->getStateUsing(function ($record) {
                        $items = $record->orderItems;

                        $html = $items->map(function ($item) {
                            $product = $item->product;

                            if (!$product || !$product->images) {
                                return "<div class='flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full'>
                                <span class='text-xs text-gray-400'>No image</span>
                            </div>";
                            }

                            // For diff_size products, find the image that matches the ordered size
                            if ($product->prod_unit === 'diff_size' && $item->size) {
                                $sizeImage = $product->images->first(function ($image) use ($item) {
                                    return isset($image->sizes) && $image->sizes === $item->size;
                                });

                                if ($sizeImage) {
                                    $imageUrl = asset(Storage::url($sizeImage->url));
                                    return "<div class='flex items-center justify-center'><img src='{$imageUrl}' class='w-8 h-8 object-cover rounded-full border border-gray-200'></div>";
                                }
                            }

                            // For all other cases, get the primary image or first available image
                            $imagePath = $product->images->first(function ($image) {
                                return $image->is_primary === true;
                            })->url ?? $product->images->first()->url ?? null;

                            if ($imagePath) {
                                $imageUrl = asset(Storage::url($imagePath));
                                return "<div class='flex items-center justify-center'><img src='{$imageUrl}' class='w-8 h-8 object-cover rounded-full border border-gray-200'></div>";
                            }

                            return "<div class='flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full'>
                            <span class='text-xs text-gray-400'>No image</span>
                        </div>";
                        })->filter()->join('');

                        return $html;
                    })
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('orderItems')
                    ->label('Quantity')
                    ->formatStateUsing(function ($record) {
                        $items = $record->orderItems;

                        $html = $items->map(function ($item) {
                            $qty = number_format($item->quantity, 0) ?? 0;
                            $unit = $item->product->prod_unit ?? '';
                            $weight = $item->product->prod_weight ?? 0;

                            if ($unit === 'pcs') {
                                $quantityText = "{$qty} pcs";
                            } elseif ($unit === 'diff_size') {
                                $quantityText = "{$qty} × " . (ucwords($item->size) ?? 'N/A');
                            } else {
                                $quantityText = "{$qty} × " . number_format($weight, 2) . $unit;
                            }

                            return "<div class='min-h-[50px] flex items-center text-sm text-gray-700 font-medium'>" .
                                $quantityText .
                                "</div>";
                        })->join('');

                        return $html;
                    })
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('notes')
                    ->limit(70)
                    ->html()
                    ->label('Notes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($state) => $state ? "<div class='text-sm text-gray-600 italic'>" . nl2br(e($state)) . "</div>" : '<span class="text-gray-400 text-sm">No notes</span>'),

                TextColumn::make('order_status')
                    ->label('Order Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => OrderStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown')
                    ->color(fn($state) => OrderStatusEnum::tryFrom($state)?->getColor() ?? 'gray')
                    ->icon(fn($state) => OrderStatusEnum::tryFrom($state)?->getIcon() ?? null)
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => PaymentStatusEnum::tryFrom($state)?->getLabel() ?? 'Unknown')
                    ->color(fn($state) => PaymentStatusEnum::tryFrom($state)?->getColor() ?? 'gray')
                    ->icon(fn($state) => PaymentStatusEnum::tryFrom($state)?->getIcon() ?? null)
                    ->sortable(),

                TextColumn::make('shipping_method')
                    ->label('Shipping Method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn(string $state): string => ($state == 'gcash' || $state == 'paymaya' || $state == 'grab_pay' || $state == 'card') ? 'E-Wallet/' . strtoupper($state) : strtoupper($state))
                    ->color(fn($state) => $state == 'gcash' || $state == 'paymaya' || $state == 'grab_pay' || $state == 'card' ? 'success' : 'primary'),

                TextColumn::make('created_at')
                    ->sortable()
                    ->formatStateUsing(fn($state) => "<span >" . Carbon::parse($state)->format('M d, Y g:i A') . "</span>")
                    ->html()
                    ->label('Ordered At'),

                TextColumn::make('shipping_price')
                    ->label('Shipping Cost')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return $state == 0 ?
                            "<span >Free Shipping</span>" :
                            "<span >₱" . number_format($state, 2) . "</span>";
                    })
                    ->html()
                    ->badge()
                    ->color(fn($record) => $record->shipping_price == 0 ? 'success' : 'info'),

                TextColumn::make('total')
                    ->label('Total Amount')
                    ->sortable()
                    ->formatStateUsing(fn($state) => "<span >₱" . number_format($state, 2) . "</span>")
                    ->html(),

            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options(PaymentStatusEnum::class)
                    ->indicator('Payment Status'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('date')
                            ->label('Date Ordered')
                            ->closeOnDateSelection()
                            ->nullable()
                            ->live()
                            ->displayFormat('M d, Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['date'])) {
                            return null;
                        }
                        return 'Date Ordered: ' . Carbon::parse($data['date'])->format('M d, Y');
                    })
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->slideOver()
                    ->button()
                    ->label('Filter')
                    ->icon('heroicon-o-funnel')
                // ->color('gray'),
            )
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),

                    Action::make('update_order_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->requiresConfirmation()
                        ->tooltip('Update Order and Payment status')
                        ->modalHeading(fn($record) => 'Confirm Status Update')
                        ->modalDescription(fn($record) => 'Are you sure you want to update the status of ' . $record->user->name . "'s order?")
                        ->color('warning')
                        ->modalSubmitActionLabel('Confirm Update')
                        ->modalWidth('2xl')
                        ->form([
                            ToggleButtons::make('order_status')
                                ->options(OrderStatusEnum::class)
                                ->default(fn($record) => $record->order_status)
                                ->dehydrated()
                                ->inline()
                                ->required()
                                ->label('Order Status'),

                            ToggleButtons::make('payment_status')
                                ->options(PaymentStatusEnum::class)
                                ->default(fn($record) => $record->payment_status)
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
                                ->title('Status Updated Successfully')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                // ->tooltip('Actions')
                // ->button()
                // ->color('gray')
                // ->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                    BulkAction::make('update_order_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->requiresConfirmation()
                        ->tooltip('Update Order and Payment status')
                        ->modalHeading('Confirm Status Update')
                        ->modalDescription('Are you sure you want to update the status of all selected orders?')
                        ->color('warning')
                        ->modalSubmitActionLabel('Confirm Update')
                        ->modalWidth('2xl')
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
                            foreach ($records as $record) {
                                $record->update([
                                    'order_status' => $data['order_status'],
                                    'payment_status' => $data['payment_status'],
                                ]);
                            }
                            Notification::make()
                                ->title('Status Updated Successfully')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-m-plus')
                    ->label(__('New Order'))
                    ->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No Orders Found')
            ->emptyStateDescription('Create your first order to get started.')
            // ->striped()
            ->deferLoading();
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
                        TextEntry::make('user.address.complete_address')->label('Address'),
                        TextEntry::make('billingAddress.bil_complete_address')->label('Billing Address'),
                    ])
                    ->collapsible()
                    ->columns(3),

                InfoSection::make('Order Details')
                    ->icon('heroicon-o-shopping-cart')
                    ->iconColor('primary')
                    ->schema([
                        TextEntry::make('order_status')->label('Order Status')
                            ->color(fn($state) => OrderStatusEnum::tryFrom($state)?->getColor())
                            ->icon(fn($state) => OrderStatusEnum::tryFrom($state)?->getIcon())
                            ->formatStateUsing(fn($state) => OrderStatusEnum::tryFrom($state)?->getLabel()),

                        TextEntry::make('payment_status')->label('Payment Status')
                            ->color(fn($state) => PaymentStatusEnum::tryFrom($state)?->getColor())
                            ->icon(fn($state) => PaymentStatusEnum::tryFrom($state)?->getIcon())
                            ->formatStateUsing(fn($state) => PaymentStatusEnum::tryFrom($state)?->getLabel()),

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

                                TextEntry::make('product.shipping_cost')->label('Shipping Cost')
                                    ->formatStateUsing(function ($state, $record) {
                                        if ($record->product->prod_requires_shipping === true && $state > 0) {
                                            return '₱ ' . number_format($state, 2);
                                        } else {
                                            return 'Free Shipping';
                                        }
                                    })->badge(),

                                ImageEntry::make('product_image')
                                    ->label('Product Image')
                                    ->width(50)
                                    ->height(50)
                                    ->extraAttributes(['style' => 'object-fit: cover; border-radius: 0.5rem;'])
                                    ->getStateUsing(function ($record) {
                                        $product = $record->product;

                                        if (!$product || !$product->images) {
                                            return null;
                                        }
                                        // For diff_size products, find the image that matches the ordered size
                                        if ($product->prod_unit === 'diff_size' && $record->size) {
                                            $sizeImage = $product->images->first(function ($image) use ($record) {
                                                // Check if the image has the matching size
                                                // Adjust this based on how sizes are stored in your images
                                                return isset($image->sizes) && $image->sizes === $record->size;
                                            });

                                            // Return the size-specific image if found
                                            if ($sizeImage) {
                                                return $sizeImage->url;
                                            }
                                        }

                                        // For all other cases, return the first product image
                                        return $product->images->first()->url ?? null;
                                    }),
                                TextEntry::make('price')->label('Price')->money('PHP')
                                    ->badge()
                                    ->formatStateUsing(function ($state, $record) {
                                        $product = $record->product;

                                        if (!$product) {
                                            return number_format($state, 2);
                                        }

                                        // For diff_size products with size-specific pricing
                                        if ($product->prod_unit === 'diff_size' && $record->size && $product->images) {
                                            // Find the image that has both the matching size and price
                                            $sizeImage = $product->images->first(function ($image) use ($record) {
                                                return isset($image->sizes) &&
                                                    $image->sizes === $record->size &&
                                                    isset($image->price);
                                            });

                                            // Use the size-specific price if found
                                            if ($sizeImage) {
                                                return '₱ ' . number_format($sizeImage->price, 2);
                                            }
                                        }

                                        // Fallback to the original price
                                        return '₱ ' . number_format($product->prod_price, 2);
                                    }),
                                TextEntry::make('quantity')->label('Quantity')
                                    ->formatStateUsing(function ($state, $record) {
                                        $qty = number_format($state, 0);
                                        $unit = $record->product->prod_unit ?? '';
                                        $weight = $record->product->prod_weight ?? 0;

                                        if ($unit === 'pcs') {
                                            return "{$qty}";
                                        }
                                        if ($unit === 'diff_size') {

                                            return "{$qty} - " . (ucwords($record->size) ?? 'N/A');
                                        }

                                        return "{$qty} × " . number_format($weight, 2) . $unit;
                                    })
                                    ->badge(),

                            ])
                            //->collapsible()
                            ->columns(2)
                            ->columnSpanFull(),

                        TextEntry::make('shipping_price')->label('Total Shipping Cost')->money('PHP')
                            ->badge()
                            ->color(fn($state) => $state == 0 ? 'success' : 'primary')
                            ->formatStateUsing(fn($state) => $state == 0 ? 'Free Shipping' : number_format($state, 2)),
                        TextEntry::make('total')->label('Grand Total')->money('PHP')
                            ->badge()
                            ->formatStateUsing(fn($state) => number_format($state, 2)),
                    ])
                    ->columns(2)->collapsible(),
            ]);
    }
}
