<?php

namespace App\Filament\Resources\DegreeResource\Pages;

use App\Filament\Resources\DegreeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDegree extends CreateRecord
{
    protected static string $resource = DegreeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
