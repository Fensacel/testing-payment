<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Kode Promo';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Promo')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->helperText('Kode promo (huruf besar, tanpa spasi)'),

                Forms\Components\Select::make('discount_type')
                    ->label('Tipe Diskon')
                    ->options([
                        'percentage' => 'Persentase (%)',
                        'fixed' => 'Nominal Tetap (Rp)',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('discount_value')
                    ->label('Nilai Diskon')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix(fn ($get) => $get('discount_type') === 'percentage' ? '%' : 'Rp')
                    ->helperText(fn ($get) => $get('discount_type') === 'percentage' ? 'Maksimal 100%' : 'Nominal dalam Rupiah'),

                Forms\Components\TextInput::make('max_usage')
                    ->label('Maksimal Penggunaan')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(100)
                    ->helperText('Berapa kali kode ini bisa digunakan'),

                Forms\Components\DateTimePicker::make('valid_from')
                    ->label('Berlaku Dari')
                    ->nullable()
                    ->helperText('Kosongkan jika langsung aktif'),

                Forms\Components\DateTimePicker::make('valid_until')
                    ->label('Berlaku Sampai')
                    ->nullable()
                    ->helperText('Kosongkan jika tidak ada batas waktu'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'Persentase' : 'Nominal')
                    ->color(fn ($state) => $state === 'percentage' ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Nilai')
                    ->formatStateUsing(fn ($record) => 
                        $record->discount_type === 'percentage' 
                            ? $record->discount_value . '%' 
                            : 'Rp ' . number_format($record->discount_value, 0, ',', '.')
                    ),

                Tables\Columns\TextColumn::make('usage')
                    ->label('Penggunaan')
                    ->formatStateUsing(fn ($record) => $record->used_count . ' / ' . $record->max_usage)
                    ->badge()
                    ->color(fn ($record) => $record->used_count >= $record->max_usage ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Berlaku Sampai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Tidak terbatas'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
