<?php

namespace App\Filament\Resources\EnteranceExamResource\Pages;

use App\Filament\Resources\EnteranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnteranceExams extends ListRecords
{
    protected static string $resource = EnteranceExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
