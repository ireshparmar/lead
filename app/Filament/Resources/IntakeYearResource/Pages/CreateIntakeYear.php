<?php

namespace App\Filament\Resources\IntakeYearResource\Pages;

use App\Filament\Resources\IntakeYearResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIntakeYear extends CreateRecord
{
    protected static string $resource = IntakeYearResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
