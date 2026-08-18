<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('medium'),

                TextColumn::make('assignee.name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unassigned'),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Task::PRIORITIES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Task::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'done' => 'success',
                        'in_progress' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                // Only open tasks turn red. A task finished after its due date
                // is late, not outstanding, so it should not demand attention.
                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('No due date')
                    ->color(fn ($state, Task $record): ?string => $state
                        && $record->status !== 'done'
                        && $state->isPast()
                            ? 'danger'
                            : null),

                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('due_date')
            ->filters([
                SelectFilter::make('status')
                    ->options(Task::STATUSES),

                SelectFilter::make('priority')
                    ->options(Task::PRIORITIES),

                SelectFilter::make('user_id')
                    ->label('Assignee')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue()),
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
