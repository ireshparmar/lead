<?php

namespace App\Filament\Resources\GenerateStudentInvoiceResource\Pages;

use App\Filament\Resources\GenerateStudentInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenerateStudentInvoices extends ListRecords
{
    protected static string $resource = GenerateStudentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
