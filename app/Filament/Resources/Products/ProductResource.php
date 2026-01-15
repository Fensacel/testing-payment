<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Models\Product;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => 
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    ),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp') 
                    ->required(),

                Forms\Components\TextInput::make('discount_percentage')
                    ->label('Diskon')
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->helperText('Masukkan persentase diskon (0-100)'),

                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('products'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Normal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Diskon')
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->label('Harga Setelah Diskon')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        if ($record->discount_percentage > 0) {
                            return $record->price - ($record->price * $record->discount_percentage / 100);
                        }
                        return $record->price;
                    })
                    ->color(fn ($record) => $record->discount_percentage > 0 ? 'success' : null),

                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Action dikosongkan agar tidak error
            ])
            ->bulkActions([
                // Bulk Action dikosongkan TOTAL agar tidak error
            ]);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}