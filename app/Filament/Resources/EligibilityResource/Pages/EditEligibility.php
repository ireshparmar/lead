<?php

namespace App\Filament\Resources\EligibilityResource\Pages;

use App\Filament\Resources\EligibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEligibility extends EditRecord
{
    protected static string $resource = EligibilityResource::class;

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
