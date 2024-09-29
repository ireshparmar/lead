<?php

namespace App\Filament\Resources\IntakeYearResource\Pages;

use App\Filament\Resources\IntakeYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntakeYears extends ListRecords
{
    protected static string $resource = IntakeYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
