<?php

namespace App\Filament\Resources\LeadDocResource\Pages;

use App\Filament\Resources\LeadDocResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadDoc extends EditRecord
{
    protected static string $resource = LeadDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
