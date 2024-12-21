<?php

namespace App\Filament\Resources\LeadInvoiceManagementResource\Pages;

use App\Filament\Resources\LeadInvoiceManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadInvoiceManagement extends EditRecord
{
    protected static string $resource = LeadInvoiceManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
