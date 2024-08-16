<?php

namespace App\Filament\Resources\EntranceExamResource\Pages;

use App\Filament\Resources\EntranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEntranceExam extends CreateRecord
{
    protected static string $resource = EntranceExamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
