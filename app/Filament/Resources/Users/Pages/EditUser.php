<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Filament fills every form field from the model, so the password input
     * would open pre-loaded with the stored bcrypt hash. It is harmless to
     * save - the `hashed` cast recognises an already-hashed value and leaves
     * it alone - but it makes the field look like it holds a real password,
     * and it defeats the "leave blank to keep it" behaviour the form relies on.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        unset($data['password']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
