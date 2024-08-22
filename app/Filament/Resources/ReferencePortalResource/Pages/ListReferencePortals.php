<?php

namespace App\Filament\Resources\ReferencePortalResource\Pages;

use App\Filament\Resources\ReferencePortalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferencePortals extends ListRecords
{
    protected static string $resource = ReferencePortalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
