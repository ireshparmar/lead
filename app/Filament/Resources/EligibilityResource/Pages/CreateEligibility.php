<?php

namespace App\Filament\Resources\EligibilityResource\Pages;

use App\Filament\Resources\EligibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEligibility extends CreateRecord
{
    protected static string $resource = EligibilityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
