<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                // The generator bound this to price_cents, so the form asked
                // for a price in cents. This edits the `price` accessor, which
                // converts to and from minor units on the model.
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('$'),

                TextInput::make('stock')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->default(0),

                // A free-text category lets 'Books', 'books' and 'boks' all
                // coexist, and nothing would ever match the filter.
                Select::make('category')
                    ->options(Product::CATEGORIES)
                    ->searchable(),

                Toggle::make('is_active')
                    ->default(true)
                    ->helperText('Inactive products stay in the catalogue but are hidden from customers.'),

                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
