<?php

namespace App\Filament\Resources\EducationLevelResource\Pages;

use App\Filament\Resources\EducationLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEducationLevel extends CreateRecord
{
    protected static string $resource = EducationLevelResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
