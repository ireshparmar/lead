<?php

namespace App\Filament\Resources\StudentInvoiceManagementResource\Pages;

use App\Filament\Resources\StudentInvoiceManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentInvoiceManagement extends ListRecords
{
    protected static string $resource = StudentInvoiceManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
