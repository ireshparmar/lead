<?php

namespace App\Filament\Resources\GenerateLeadInvoiceResource\Pages;

use App\Filament\Resources\GenerateLeadInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGenerateLeadInvoice extends EditRecord
{
    protected static string $resource = GenerateLeadInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
