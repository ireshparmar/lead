<?php

namespace App\Filament\Resources\GenerateLeadInvoiceResource\Pages;

use App\Filament\Resources\GenerateLeadInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenerateLeadInvoices extends ListRecords
{
    protected static string $resource = GenerateLeadInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
