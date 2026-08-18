<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                // Bound to the integer column, so sorting compares numbers.
                // Formatting only changes what is rendered.
                TextColumn::make('price_cents')
                    ->label('Price')
                    ->money('USD', divideBy: 100)
                    ->sortable(),

                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 20 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Product::CATEGORIES[$state] ?? '-')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options(Product::CATEGORIES),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                Filter::make('out_of_stock')
                    ->label('Out of stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock', 0)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
