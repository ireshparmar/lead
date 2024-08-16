<?php

namespace App\Filament\Resources\EntranceExamResource\Pages;

use App\Filament\Resources\EntranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntranceExam extends EditRecord
{
    protected static string $resource = EntranceExamResource::class;

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
