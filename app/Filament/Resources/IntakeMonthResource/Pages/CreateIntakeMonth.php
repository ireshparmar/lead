<?php

namespace App\Filament\Resources\IntakeMonthResource\Pages;

use App\Filament\Resources\IntakeMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIntakeMonth extends CreateRecord
{
    protected static string $resource = IntakeMonthResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
