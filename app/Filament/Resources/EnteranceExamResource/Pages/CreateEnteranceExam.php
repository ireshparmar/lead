<?php

namespace App\Filament\Resources\EnteranceExamResource\Pages;

use App\Filament\Resources\EnteranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEnteranceExam extends CreateRecord
{
    protected static string $resource = EnteranceExamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
