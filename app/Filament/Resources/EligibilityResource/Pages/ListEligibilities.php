<?php

namespace App\Filament\Resources\EligibilityResource\Pages;

use App\Filament\Resources\EligibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEligibilities extends ListRecords
{
    protected static string $resource = EligibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
