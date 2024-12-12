<?php

namespace App\Filament\Resources\LeadCommissionResource\Pages;

use App\Filament\Resources\LeadCommissionResource;
use App\Helpers\CurrencyHelper;
use App\Models\Lead;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadCommission extends CreateRecord
{
    protected static string $resource = LeadCommissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $lead = Lead::with('country')->find($data['commissionable_id']);
        $data['base_currency_rate'] = CurrencyHelper::findBaseCurrencyRate($lead->country[0]->currency);
        return $data;
    }
}
