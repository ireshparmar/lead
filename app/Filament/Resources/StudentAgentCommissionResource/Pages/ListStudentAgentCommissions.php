<?php

namespace App\Filament\Resources\StudentAgentCommissionResource\Pages;

use App\Filament\Resources\StudentAgentCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentAgentCommissions extends ListRecords
{
    protected static string $resource = StudentAgentCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
