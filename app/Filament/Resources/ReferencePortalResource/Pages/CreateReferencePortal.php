<?php

namespace App\Filament\Resources\ReferencePortalResource\Pages;

use App\Filament\Resources\ReferencePortalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReferencePortal extends CreateRecord
{
    protected static string $resource = ReferencePortalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
