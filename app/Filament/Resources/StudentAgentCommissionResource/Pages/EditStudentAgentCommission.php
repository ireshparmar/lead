<?php

namespace App\Filament\Resources\StudentAgentCommissionResource\Pages;

use App\Filament\Resources\StudentAgentCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentAgentCommission extends EditRecord
{
    protected static string $resource = StudentAgentCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
