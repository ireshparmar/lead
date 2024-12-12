<?php

namespace App\Filament\Resources\LeadCommissionResource\Pages;

use App\Filament\Resources\LeadCommissionResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeadCommissions extends ListRecords
{
    protected static string $resource = LeadCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
