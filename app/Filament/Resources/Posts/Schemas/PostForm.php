<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // The generator produced a numeric input for user_id, which
                // means typing a raw database id. relationship() renders the
                // author list by name and stores the id.
                Select::make('user_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    // Fills the slug while typing the title, but only on
                    // create: rewriting a published post's slug silently
                    // breaks every existing link to it.
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Used in the post URL.'),

                Textarea::make('excerpt')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),

                Textarea::make('body')
                    ->rows(12)
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('cover_image')
                    ->image()
                    ->imageEditor()
                    ->directory('posts')
                    ->columnSpanFull(),

                Toggle::make('is_published')
                    ->live()
                    // Publishing without a date leaves the post invisible to
                    // Post::published(), which requires both.
                    ->afterStateUpdated(function (bool $state, Set $set): void {
                        if ($state) {
                            $set('published_at', now());
                        }
                    }),

                DateTimePicker::make('published_at')
                    ->label('Publish at')
                    ->helperText('A future date schedules the post instead of publishing it now.'),
            ]);
    }
}
