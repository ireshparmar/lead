<?php

namespace App\Filament\Resources\LeadPaymentResource\Pages;

use App\Filament\Resources\LeadPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadPayment extends EditRecord
{
    protected static string $resource = LeadPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
