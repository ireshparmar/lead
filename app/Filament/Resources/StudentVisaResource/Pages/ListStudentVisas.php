<?php

namespace App\Filament\Resources\StudentVisaResource\Pages;

use App\Filament\Resources\StudentVisaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentVisas extends ListRecords
{
    protected static string $resource = StudentVisaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
