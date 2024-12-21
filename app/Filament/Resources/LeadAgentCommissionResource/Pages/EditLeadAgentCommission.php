<?php

namespace App\Filament\Resources\LeadAgentCommissionResource\Pages;

use App\Filament\Resources\LeadAgentCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadAgentCommission extends EditRecord
{
    protected static string $resource = LeadAgentCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
