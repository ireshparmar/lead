<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn($record) => Auth::user()->hasRole('Admin') || $record->created_by ===  Auth::user()->id),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }


}
