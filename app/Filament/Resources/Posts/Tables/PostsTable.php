<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->circular(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('medium'),

                // Reads through the relationship, so the column shows a name
                // rather than the raw user_id the generator produced.
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Not scheduled'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label('Published'),

                // Flagged published but dated in the future - easy to create by
                // accident, and otherwise invisible in the list.
                Filter::make('scheduled')
                    ->label('Scheduled for later')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('is_published', true)
                        ->where('published_at', '>', now())),
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
