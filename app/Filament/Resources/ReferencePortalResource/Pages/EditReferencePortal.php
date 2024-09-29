<?php

namespace App\Filament\Resources\ReferencePortalResource\Pages;

use App\Filament\Resources\ReferencePortalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferencePortal extends EditRecord
{
    protected static string $resource = ReferencePortalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
