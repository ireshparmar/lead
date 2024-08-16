<?php

namespace App\Filament\Resources\EntranceExamResource\Pages;

use App\Filament\Resources\EntranceExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntranceExams extends ListRecords
{
    protected static string $resource = EntranceExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
