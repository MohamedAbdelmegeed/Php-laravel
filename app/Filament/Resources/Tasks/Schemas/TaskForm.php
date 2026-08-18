<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->label('Assignee')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave empty for an unassigned task.'),

                Select::make('priority')
                    ->options(Task::PRIORITIES)
                    ->required()
                    ->default('medium'),

                Select::make('status')
                    ->options(Task::STATUSES)
                    ->required()
                    ->default('todo')
                    ->live()
                    // Keeps completed_at consistent with status, so
                    // Task::overdue() cannot report a finished task.
                    ->afterStateUpdated(function (string $state, Set $set): void {
                        $set('completed_at', $state === 'done' ? now() : null);
                    }),

                DatePicker::make('due_date'),

                DateTimePicker::make('completed_at')
                    ->helperText('Set automatically when the status becomes Done.'),

                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
