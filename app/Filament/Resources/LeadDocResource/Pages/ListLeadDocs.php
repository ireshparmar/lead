<?php

namespace App\Filament\Resources\LeadDocResource\Pages;

use App\Filament\Resources\LeadDocResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadDocs extends ListRecords
{
    protected static string $resource = LeadDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
