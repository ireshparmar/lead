<?php

namespace App\Filament\Resources\LeadPaymentResource\Pages;

use App\Filament\Resources\LeadPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadPayments extends ListRecords
{
    protected static string $resource = LeadPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
