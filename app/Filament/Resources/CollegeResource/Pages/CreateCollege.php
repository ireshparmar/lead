<?php

namespace App\Filament\Resources\CollegeResource\Pages;

use App\Filament\Resources\CollegeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCollege extends CreateRecord
{
    protected static string $resource = CollegeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
