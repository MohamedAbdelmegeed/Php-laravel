<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                // Without unique(), a repeated address reaches the database and
                // surfaces as an exception page instead of a field-level error.
                // ignoreRecord lets a user keep their own address when editing.
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                DateTimePicker::make('email_verified_at'),

                // The generator marks this required on both pages, which forces
                // whoever edits a user to retype their password to change their
                // name. Required on create only; on edit, an empty field is
                // dropped before save (dehydrated) so the stored hash survives.
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit'
                        ? 'Leave blank to keep the current password.'
                        : null),
            ]);
    }
}
