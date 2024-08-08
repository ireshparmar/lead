<?php

namespace App\Filament\Resources\IntakeMonthResource\Pages;

use App\Filament\Resources\IntakeMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntakeMonth extends EditRecord
{
    protected static string $resource = IntakeMonthResource::class;

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
