<?php

namespace App\Filament\Resources\IntakeMonthResource\Pages;

use App\Filament\Resources\IntakeMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntakeMonths extends ListRecords
{
    protected static string $resource = IntakeMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
