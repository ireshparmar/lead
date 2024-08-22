<?php

namespace App\Filament\Resources\CampusResource\Pages;

use App\Filament\Resources\CampusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCampus extends CreateRecord
{
    protected static string $resource = CampusResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
