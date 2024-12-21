<?php

namespace App\Filament\Resources\GenerateStudentInvoiceResource\Pages;

use App\Filament\Resources\GenerateStudentInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGenerateStudentInvoice extends EditRecord
{
    protected static string $resource = GenerateStudentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
