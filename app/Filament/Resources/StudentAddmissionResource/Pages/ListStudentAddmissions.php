<?php

namespace App\Filament\Resources\StudentAddmissionResource\Pages;

use App\Filament\Resources\StudentAddmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentAddmissions extends ListRecords
{
    protected static string $resource = StudentAddmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
