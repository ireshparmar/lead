<?php

namespace App\Filament\Resources\EnteranceExamResource\Pages;

use App\Filament\Resources\EnteranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnteranceExam extends EditRecord
{
    protected static string $resource = EnteranceExamResource::class;

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
