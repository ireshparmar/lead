<?php

namespace App\Filament\Resources\CurrencyConversationResource\Pages;

use App\Filament\Resources\CurrencyConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrencyConversation extends CreateRecord
{
    protected static string $resource = CurrencyConversationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
