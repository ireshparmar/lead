<?php

namespace App\Filament\Resources\CurrencyConversationResource\Pages;

use App\Filament\Resources\CurrencyConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurrencyConversations extends ListRecords
{
    protected static string $resource = CurrencyConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
