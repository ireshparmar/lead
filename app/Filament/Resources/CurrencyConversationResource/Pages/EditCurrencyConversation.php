<?php

namespace App\Filament\Resources\CurrencyConversationResource\Pages;

use App\Filament\Resources\CurrencyConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurrencyConversation extends EditRecord
{
    protected static string $resource = CurrencyConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
