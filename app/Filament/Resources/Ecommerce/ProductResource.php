<?php

namespace App\Filament\Resources\Ecommerce;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Pages\ViewProduct;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Icon;
use App\Models\Ecommerce\Product;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Models\Ecommerce\ProductImage;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use App\Models\Ecommerce\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use App\Filament\Resources\Ecommerce\ProductResource\Pages;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Section as ComponentsSection;
use App\Filament\Resources\Ecommerce\ProductResource\RelationManagers;
use Filament\Infolists\Components\Grid as ComponentsGrid;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Ecommerce';
   
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    // 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
   public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Product Information')
                ->description('Please provide the following details about the product.')
                ->schema([
                    Group::make()
                        ->schema([
                            TextInput::make('prod_name')
                                ->label('Product Name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->live(onBlur: true)
                                ->unique(Product::class, 'prod_name', ignoreRecord: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('prod_slug', Str::slug($state))),

                            TextInput::make('prod_slug')
                                ->label('Product Slug')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->unique(Product::class, 'prod_slug', ignoreRecord: true),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Group::make()
                        ->schema([
                            TextInput::make('prod_sku')
                                ->label('Product SKU')
                                ->default(function () {
                                    do {
                                        $sku = 'SKU-' . rand(1000, 9999) . '-' . strtoupper(Str::random(4));
                                    } while (Product::where('prod_sku', $sku)->exists());
                                    return $sku;
                                })
                                ->disabled()
                                ->dehydrated()
                                ->unique(Product::class, 'prod_sku', ignoreRecord: true)
                                ->suffixAction(
                                    FormAction::make('regenerateSku')
                                        ->label('Regenerate SKU')
                                        ->icon('heroicon-s-arrow-path')
                                        ->action(function (Set $set) {
                                            do {
                                                $sku = 'SKU-' . rand(1000, 9999) . '-' . strtoupper(Str::random(4));
                                            } while (Product::where('prod_sku', $sku)->exists());
                                            $set('prod_sku', $sku);
                                        })
                                ),

                            Select::make('prod_unit')
                                ->label('Product Unit')
                                ->required()
                                ->options([
                                    'diff_size' => 'Different Sizes (small, medium, large,etc)',
                                    'pcs' => 'Piece (pcs)',
                                    'kg' => 'Kilograms (kg)',
                                    'g' => 'Grams (g)',
                                ])
                                ->default('pcs')
                                ->reactive(),

                            TextInput::make('prod_weight')
                                ->label(fn ($get) => $get('prod_unit') === 'kg' ? 'Product Weight kilograms (kg)' : 'Product Weight grams (g)')
                                ->required()
                                ->numeric()
                                ->hidden(fn ($get) => $get('prod_unit') === 'pcs' || $get('prod_unit') === 'diff_size'),

                            TextInput::make('prod_quantity')
                                ->label('Product Quantity')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->hidden(fn ($get) => $get('prod_unit') === 'diff_size')
                                ->default(1),

                            TextInput::make('prod_price')
                                ->label('Product Price')
                                ->minValue(1)
                                ->required()
                                ->prefix('PHP')
                                ->numeric()
                                ->hidden(fn ($get) => $get('prod_unit') === 'diff_size')
                                ->default(0),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),

                    Section::make('Category & Visibility')
                        ->schema([
                            Select::make('productCategories')
                                ->label('Product Category')
                                ->multiple()
                                ->relationship(
                                    name: 'productCategories',
                                    titleAttribute: 'prod_cat_name'
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->prod_cat_name))
                                ->preload()
                                ->searchable()
                                ->required()
                                ->createOptionForm([
                                    Section::make('Product Category Details')->schema([
                                        TextInput::make('prod_cat_name')
                                            ->label('Category Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ProductCategory::class, 'prod_cat_name', ignoreRecord: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('prod_cat_slug', Str::slug($state)))
                                            ->columnSpan(1),

                                        TextInput::make('prod_cat_slug')
                                            ->label('Category Slug')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->unique(ProductCategory::class, 'prod_cat_slug', ignoreRecord: true)
                                            ->columnSpan(1),

                                        MarkdownEditor::make('prod_cat_description')
                                            ->label('Category Description')
                                            ->maxLength(65535)
                                            ->columnSpanFull(),
                                    ]),
                                ]),

                            ToggleButtons::make('prod_requires_shipping')
                                ->label('Requires Shipping?')
                                ->boolean()
                                ->reactive()
                                ->dehydrated()
                                ->grouped()
                                ->colors([false => 'warning', true => 'success'])
                                ->icons([false => 'heroicon-m-x-circle', true => 'heroicon-m-check-circle'])
                                ->default(true),

                            TextInput::make('shipping_cost')
                                ->label('Shipping Cost')
                                ->numeric()
                                ->prefix('PHP')
                                ->minValue(20)
                                ->maxValue(100)
                                ->required(fn ($get) => $get('prod_requires_shipping') == true)
                                ->hidden(fn ($get) => $get('prod_requires_shipping') == false),

                            ToggleButtons::make('is_visible_to_market')
                                ->label('Visible in Market?')
                                ->boolean()
                                ->grouped()
                                ->dehydrated()
                                ->colors([false => 'warning', true => 'success'])
                                ->icons([false => 'heroicon-m-x-circle', true => 'heroicon-m-check-circle'])
                                ->default(false),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make('Product Description')
                ->schema([
                    Textarea::make('prod_short_description')
                        ->label('Short Description')
                        ->maxLength(555)
                        ->columnSpanFull(),

                    RichEditor::make('prod_description')
                        ->label('Detailed Description')
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Section::make(fn ($get) => $get('prod_unit') !== 'diff_size' ? 'Product Images' : 'Product Image, Sizes, Quantities and Prices')
                ->schema([
                    Repeater::make('images')
                        ->label('Upload Product Images')
                        ->relationship(name: 'images')
                        ->schema([
                            FileUpload::make('url')
                                ->label('Image')
                                ->image()
                                ->imageEditor()
                                ->preserveFilenames()
                                ->visibility('public')
                                ->imageEditorAspectRatios([null, '16:9', '4:3'])
                                ->maxSize(2048)
                                ->required(),
                                

                            Select::make('sizes')
                                ->label('Sizes')
                                ->required()
                                ->options([
                                    'extra_small' => 'Extra Small',
                                    'small' => 'Small',
                                    'medium' => 'Medium',
                                    'large' => 'Large',
                                    'extra_large' => 'Extra Large',
                                ])
                            ->hidden(fn ($get) => $get('../../prod_unit') !== 'diff_size'),
                            
                            TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->hidden(fn ($get) => $get('../../prod_unit') !== 'diff_size'),

                            TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('PHP')
                            ->minValue(1)
                            ->required()
                            ->hint('Price may vary in size')
                            ->hintColor('warning')
                            ->hidden(fn ($get) => $get('../../prod_unit') !== 'diff_size'),

                            ToggleButtons::make('is_primary')
                                ->label('Primary Image?')
                                ->boolean()
                                ->grouped()
                                ->colors([false => 'warning', true => 'success'])
                                ->icons([false => 'heroicon-m-x-circle', true => 'heroicon-m-check-circle'])
                                ->default(false)
                               
                            //    ->disabled(function ($get, $state) {
                            //     $images = $get('../../images') ?? [];

                            //     // If this current toggle is already set to true, keep it enabled
                            //     if ($state === true) {
                            //         return false;
                            //     }

                            //     // Check if any other image is already primary
                            //     $hasPrimary = collect($images)
                            //         ->filter(fn ($img) => !empty($img['is_primary']) && $img['is_primary'] === true)
                            //         ->isNotEmpty();

                            //     return $hasPrimary;
                            // })
                            // ->live(),

                         
                               
                        ])
                        
                        ->columns(2)
                        ->addActionLabel('Add Another')
                        ->collapsible()
                        ->deletable(fn ($get) => count($get('images')) > 1)
                        // ->maxItems(5)
                        ->reorderable(),
                ]),
        ]);
}


  
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            // Grid::make([
            //     'lg' => 3,
            //     '2xl' => 3,
            // ]),
            
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\Layout\Split::make([
                    TextColumn::make('prod_sku')
                        ->label('SKU')
                        ->searchable()
                        ->badge()
                        ->copyable()
                        ->color('success')
                        ->weight(FontWeight::Bold),

                        
                        
                        
                    
                    ]),

                ImageColumn::make('images.url')
                        ->label('Primary Image')
                        // ->height('100%')
                        // ->width('100%')
                        ->height(200)
                        ->width(200)
                        ->limit(1)
                        ->getStateUsing(fn ($record) => $record->images()->where('is_primary', true)?->value('url')   ?? $record->images()->orderBy('created_at')->value('url'))
                        ->extraAttributes(['class' => 'rounded-lg']),
                
                TextColumn::make('prod_name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->formatStateUsing(function ($record){
                        if($record->prod_unit === 'kg')
                        {
                            return ucwords($record->prod_name) .' - '. $record->prod_weight.' '.$record->prod_unit;
                        }

                        if($record->prod_unit === 'g')
                        {
                            return ucwords($record->prod_name) .' - '. $record->prod_weight.' '.$record->prod_unit;
                        }
                    //    if ($record->prod_unit === 'diff_size') {
                    //         $sizes = $record->images->pluck('sizes')->filter()->unique()->implode(', ');
                    //         return ucwords($record->prod_name) . ' - ' . $sizes;
                    //     }
                        
                        return ucwords($record->prod_name);
                       
                    }),

                   
                

                
                Tables\Columns\Layout\Split::make([
                    TextColumn::make('prod_price')
                        ->label('Price')
                        ->sortable()
                        ->hidden(fn ($record) => $record?->prod_unit === 'diff_size')
                        ->money('PHP'),


                 
                    
                    TextColumn::make('prod_quantity')
                        ->label('Quantity')
                        ->badge()
                        ->hidden(fn ($record) => $record?->prod_unit === 'diff_size')
                        ->formatStateUsing(fn (string $state) : string => number_format($state,0))
                        ->sortable()
                        ->color('info'),
                        

                    
                    TextColumn::make('prod_status')
                        ->label('Status')
                        ->hidden(fn ($record) => $record?->prod_unit === 'diff_size')
                        ->getStateUsing(fn ($record) => match (true) {
                            $record->prod_quantity > 10 => 'In&nbsp;Stock',
                            $record->prod_quantity > 0 && $record->prod_quantity <= 10 => 'Low&nbsp;in&nbsp;Stock',
                            default => 'Out&nbsp;of&nbsp;Stock',
                        })->html() ->badge()
                        ->color(fn ($record) => match (true) {
                            $record->prod_quantity > 10 => 'success',
                            $record->prod_quantity > 0 && $record->prod_quantity <= 10 => 'warning',
                            default => 'danger',
                        }),



                    
                        
                ]),
            ])->space(3),

            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([

                    TextColumn::make('productCategories.prod_cat_name')
                    ->badge()
                    ->label('Product Category')
                    // ->sortable()
                    ->color('warning')
                    ->formatStateUsing(fn (string $state) : string => ucwords($state)),


                ]),

               
            ])->collapsible(),
        

        ])
        ->contentGrid([
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
        ])
        ->paginated([
            9,
            18,
            36,
            'all',
        ])
        ->filters([
            SelectFilter::make('productCategories.prod_cat_name')
                ->relationship('productCategories', 'prod_cat_name')
                ->label('Product Category')
                ->options(ProductCategory::query()->pluck('prod_cat_name', 'id'))
                ->searchable()
                ->preload()
                ->multiple(),

            SelectFilter::make('is_visible_to_market')
            ->label('Product Visibility')
            ->options([
                '1' => 'Visible to Market',
                '0' => 'Not Visible to Market',
            ]),

            // SelectFilter::make('prod_stock')
            // ->label('Product Stock')
            // ->options([
            //     'out_of_stock' => 'Out of Stock',
            //     'low_stock' => 'Low in Stock',
            //     'in_stock' => 'In Stock',
            // ])
            // ->query(function ($query, $state) {
            //     $value = $state['value'] ?? null;

            //     if ($value === 'out_of_stock') {
            //          return $query->where('prod_quantity', 0);
            //     }

            //     if ($value === 'low_stock') {
            //         return $query->whereBetween('prod_quantity', [1, 10]);
            //     }

            //     if ($value === 'in_stock') {
            //         return $query->where('prod_quantity', '>', 10);
            //     }

            //     return $query;
            // }),
                
         
        ])
        ->actions([
            //Tables\Actions\ViewAction::make()->icon('heroicon-m-eye')->label(''),
           //Tables\Actions\ActionGroup::make([
                // Tables\Actions\ViewAction::make()->icon('heroicon-m-eye')->label(''),
                // Tables\Actions\EditAction::make()->icon('heroicon-m-pencil')->label(''),
        Tables\Actions\Action::make('show_diff_sizes')
        ->label('Show Sizes')
        ->icon('heroicon-m-eye')
        ->color('success')
        ->hidden(fn ($record) => $record?->prod_unit !== 'diff_size')
        ->modalHeading(fn ($record) => $record->prod_name . ' - Available Sizes')
        ->modalSubmitAction(false)
        ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
        ->modalContent(function ($record) {
            //if prod unit is not diff size retuirn null 
            if ($record?->prod_unit !== 'diff_size') return null;
            
            //get product images
            $sizesHtml = $record->images
                //kwaun lng ang my sizes like small medium large etc.
                ->filter(fn ($image) => !empty($image->sizes))
                ->map(function ($image) {
                    //remove special characters
                    $cleanSize = str_replace('_', ' ', $image->sizes);
                    $cleanSize = preg_replace('/[^\w\s]/', '', $cleanSize);
                    $cleanSize = ucwords($cleanSize);
                    
                    $quantity = $image->quantity ?? 0;
                    $price = number_format($image->price, 2); // format price 2 decimal places lang
                    
                    // quantity checker
                    $statusBadge = match(true) {
                        $quantity === 0 => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Out of Stock</span>',
                        $quantity < 10 => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Low Stock ('.$quantity.')</span>',
                        default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">In Stock ('.$quantity.')</span>'
                    };
                    
                    //output price, img and sizes
                    return '
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center space-x-4">
                            <img src="' . e(\Illuminate\Support\Facades\Storage::url($image->url)) . '" class="w-16 h-16 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-lg text-gray-900 dark:text-gray-100">'.$cleanSize.'</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 text-lg">₱'.$price.'</span>
                                </div>
                                <div>'.$statusBadge.'</div>
                            </div>
                        </div>
                    ';
                })
                ->unique()
                ->implode('');
            //output all 
            return new HtmlString('
                <div class="space-y-4">
                    '.$sizesHtml.'
                </div>
            ');
        })

                // Tables\Actions\DeleteAction::make()->icon('heroicon-m-trash')->label(''),
            //])->tooltip('Actions')
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('prod_requires_shipping')
                ->label('Free Shipping')
                ->icon('heroicon-o-truck')
                ->action(function (Collection $records): void {
                    $records->each(function ($record) {
                        $record->update([
                            'prod_requires_shipping' => false,
                        ]);
                    });
                })
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-truck')
                ->modalHeading('This action will make the selected product(s) free shipping.')
                ->modalDescription('Are you sure you want to make the selected product(s) free shipping?')
                ->modalSubmitActionLabel('Yes')
                ->color('warning'),

               

                Tables\Actions\BulkAction::make('is_visible_to_market')
                ->label('Visible to Market')
                ->icon('heroicon-o-eye')
                ->action(function (Collection $records): void {
                    $records->each(function ($record) {
                        $record->update([
                            'is_visible_to_market' => !$record->is_visible_to_market,
                        ]);
                    });
                })
                ->deselectRecordsAfterCompletion()
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-eye')
                ->modalHeading('This action will make the selected product(s) available to market.')
                ->modalDescription('Are you sure you want to make the selected product(s) available to market?')
                ->modalSubmitActionLabel('Yes')
                ->color('success'),

            ]),
        ])
        ->deferLoading()
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('New Product')),
        ])
        ->emptyStateIcon('heroicon-o-squares-plus')
        ->emptyStateHeading('No Products are created')
        ->defaultSort('created_at', 'desc');
}



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
           Pages\EditProduct::class,
        ]);
    }
    
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        // Product Overview Section
        InfoSection::make('Product Overview')->icon('heroicon-o-funnel')->schema([
            ComponentsGroup::make([
                // Image on the left
                ImageEntry::make('images.url')
                    ->hiddenLabel()
                    ->limit(function ($record) {
                        if($record->prod_unit === 'diff_size') {
                            return 1;
                        }
                        return 4;
                    })
                    ->width(200)
                    ->height(200)
                    // ->hidden(fn($record) => $record->prod_unit === 'diff_size')
                    ->extraAttributes(['style' => 'object-fit: cover; border-radius: 0.5rem;']),

                // Product name and SKU on the right
                ComponentsGroup::make([
                    TextEntry::make('prod_name')
                        ->label('Product Name')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::ExtraBold)
                        ->formatStateUsing(fn (string $state): string => ucwords($state)),

                    TextEntry::make('prod_sku')
                        ->label('SKU')
                        ->size(TextEntry\TextEntrySize::Medium)
                        ->weight(FontWeight::Bold)
                        ->badge()
                        ->color('success')
                        ->copyable(),
                ])->columns(1),
            ])->columns(2)->columnSpanFull(),
        ])->columns(1)->compact()->collapsible(),

        // Product Details Section
        ComponentsSection::make('Product Details')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextEntry::make('prod_short_description')
                    ->markdown()
                    ->label('Short Description:')
                    ->html()
                    ->columnSpan(1),

                TextEntry::make('prod_description')
                    ->markdown()
                    ->label('Long Description:')
                    ->html()
                    ->columnSpan(1),
            ])->collapsible()
            ->columns(2),

 // Size Variant Section (only shown when prod_unit is diff_size)
ComponentsSection::make('Size Variants')
    ->icon('heroicon-o-arrows-pointing-out')
    ->schema([
        ComponentsGrid::make()
            ->schema(function ($record) {
                // Only show if product has diff_size unit
                if ($record->prod_unit !== 'diff_size') {
                    return [];
                }

                return $record->images->map(function ($variant) {
                    return ComponentsGroup::make([
                        ComponentsGroup::make([
                            // Display variant image if available
                            ImageEntry::make('image')
                                ->state($variant->url)
                                ->hiddenLabel()
                                 ->width('100%')
                                ->height(120) 
                                // ->extraAttributes([
                                //     'style' => 'object-fit: cover; border-radius: 0.5rem;',
                                //     'class' => 'border border-gray-200 dark:border-gray-600' // Dark mode border
                                // ])
                                ->hidden(fn ($state) => empty($state)),
                            
                            // Size variant details - redesigned layout
                            ComponentsGroup::make([
                                // Size header
                                TextEntry::make('size_header')
                                    ->hiddenLabel()
                                    ->state('Size: ' . preg_replace('/[^a-zA-Z0-9]/', ' ', ucwords($variant->sizes)))
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Small)
                                    ->columnSpanFull(),
                                
                                // Price - made more prominent
                                TextEntry::make('price')
                                    ->state($variant->price)
                                    ->label('Price:')
                                    ->size(TextEntry\TextEntrySize::Medium)
                                    ->weight(FontWeight::Bold)
                                    ->money('PHP')
                                    ->color('success')
                                    ->columnSpanFull(),

                                // Quantity with conditional styling
                                TextEntry::make('quantity')
                                    ->state(function () use ($variant) {
                                        if ($variant->quantity > 10) {
                                            return $variant->quantity . ' In Stock';
                                        }
                                        if ($variant->quantity > 0 && $variant->quantity <= 10) {
                                            return $variant->quantity . ' Low Stock';
                                        }
                                        return 'Out of Stock';
                                    })
                                    ->label('Availability:')
                                    ->badge()
                                    ->color(function () use ($variant) {
                                        if ($variant->quantity > 10) return 'success';
                                        if ($variant->quantity >0  && $variant->quantity <= 10) return 'warning';
                                        return 'danger';
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->extraAttributes(['class' => 'space-y-2 dark:text-gray-100']), // Dark mode text
                        ])
                        ->columns(2)
                        ->extraAttributes([
                            'class' => 'p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md dark:hover:shadow-lg transition-shadow',
                            'style' => 'gap: 1rem;'
                        ]),
                    ]);
                })->toArray();
            })
            ->columns(2)
            ->extraAttributes(['class' => 'gap-4']),
    ])
    ->hidden(fn ($record) => $record->prod_unit !== 'diff_size')
    ->collapsible(),

        // Pricing Section (hidden when prod_unit is diff_size)
        ComponentsSection::make('Pricing and Category')
            ->icon('heroicon-o-document-currency-bangladeshi')
            ->schema([
                // TextEntry::make('prod_old_price')
                //     ->label('Old Price')
                //     ->badge()
                //     ->color('warning')
                //     ->hidden(fn ($record) => $record->prod_unit === 'diff_size'),

                TextEntry::make('prod_price')
                    ->label('Price')
                    ->badge()
                    ->color('success')
                    ->money('PHP')
                    ->hidden(fn ($record) => $record->prod_unit === 'diff_size'),

                TextEntry::make('productCategories')
                    ->label('Categories')
                    ->html() 
                    ->formatStateUsing(function ($state, $record) {
                        return $record->productCategories
                            ->pluck('prod_cat_name')
                            ->map(fn ($name) => "<span style='display:inline-block; padding:2px 6px; background-color:#d1fae5; color:#065f46; border-radius:9999px; font-size:12px; margin-right:4px;'>{$name}</span>")
                            ->implode(' ');
                    }),
            ])->columns(2)->collapsible(),

        // Inventory Section (hidden when prod_unit is diff_size)
        ComponentsSection::make('Inventory')
            ->icon('heroicon-o-archive-box')
            ->schema([
                TextEntry::make('prod_quantity')
                    ->label('Available Quantity')
                    ->badge()
                    ->color('info')
                    ->hidden(fn ($record) => $record->prod_unit === 'diff_size'),

                TextEntry::make('prod_unit')
                    ->label('Unit Type')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pcs' => 'Piece(s)',
                            'kg' => 'Kilogram(s)',
                            'g' => 'Gram(s)',
                            'diff_size' => 'Different Sizes',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color('gray'),
            ])->columns(2)->collapsible()->hidden(fn ($record) => $record->prod_unit === 'diff_size'),

        // Shipping Section
        ComponentsSection::make('Shipping Info')
            ->icon('heroicon-o-truck')
            ->schema([
                TextEntry::make('prod_requires_shipping')
                    ->label('Requires Shipping?')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                TextEntry::make('prod_weight')
                    ->label(fn ($component) => $component->getRecord()->prod_unit === 'g' ? 'Weight (g)' : 'Weight (kg)')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($component, $state) => $state . ' ' . ($component->getRecord()->prod_unit === 'g' ? 'g' : 'kg'))
                    ->hidden(fn ($component) => in_array($component->getRecord()->prod_unit, ['pcs', 'diff_size'])),

              TextEntry::make('shipping_cost')
                ->label('Shipping Cost')
                ->badge()
                ->default('')
                ->formatStateUsing(function ($component) {
                    $record = $component->getRecord();
                    
                    return !$record->prod_requires_shipping ? 'Free Shipping' : 'PHP ' . number_format($record->shipping_cost, 2);
                 
                  
                })
                ->color(fn ($component) => !$component->getRecord()->prod_requires_shipping ? 'success' : 'info')
            ])->columns(2)->collapsible(),

        // Market Visibility
        InfoSection::make('Item Visibility')->icon('heroicon-o-eye-slash')->schema([
            TextEntry::make('is_visible_to_market')
                ->label('Visible to Market?')
                ->badge()
                ->color(fn ($state) => $state ? 'success' : 'warning')
                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
        ])->columns(2)->collapsible(),
    ]);
}







    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

   
}
