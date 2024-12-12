<?php

namespace App\Filament\Resources\StudentCommissionResource\Pages;

use App\Filament\Resources\StudentCommissionResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStudentCommissions extends ListRecords
{
    protected static string $resource = StudentCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
