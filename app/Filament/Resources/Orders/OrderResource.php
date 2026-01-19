<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Daftar Order';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Order')
                    ->extraAttributes(['class' => 'max-w-none w-full'])
                    ->schema([
                        // Order Information
                        Forms\Components\TextInput::make('order_number')
                            ->label('No. Order')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Tanggal Order')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state instanceof \DateTime ? $state->format('d F Y, H:i') : ($state ? date('d F Y, H:i', strtotime($state)) : '-'))
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'success' => 'Lunas',
                                'failed' => 'Gagal',
                            ])
                            ->disabled()
                            ->columnSpan(1),
                        
                        // Customer Information
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Customer')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('No. Telepon')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')
                            ->disabled()
                            ->columnSpanFull(),
                        
                        // Order Items
                        Forms\Components\Repeater::make('items')
                            ->label('Barang yang Dibeli')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('Nama Produk')
                                    ->disabled(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->suffix(' pcs')
                                    ->disabled(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Satuan')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->formatStateUsing(fn ($state, $get) => number_format($get('price') * $get('quantity'), 0, ',', '.')),
                            ])
                            ->columns(4)
                            ->disabled()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                        
                        // Pricing Summary
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal Barang')
                            ->prefix('Rp')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', '.'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('promo_discount')
                            ->label('Diskon Promo')
                            ->prefix('Rp')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', '.'))
                            ->visible(fn ($get) => ($get('promo_discount') ?? 0) > 0)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('admin_fee')
                            ->label('Biaya Admin (2.5%)')
                            ->prefix('Rp')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', '.'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Pembayaran')
                            ->prefix('Rp')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                            ->columnSpanFull(),
                    ])
                    ->columns(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn ($record): string => static::getUrl('view', ['record' => $record])
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
