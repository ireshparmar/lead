<?php

namespace App\Filament\Resources\IntakeYearResource\Pages;

use App\Filament\Resources\IntakeYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntakeYear extends EditRecord
{
    protected static string $resource = IntakeYearResource::class;

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
