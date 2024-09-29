<?php

namespace App\Filament\Resources\StreamResource\Pages;

use App\Filament\Resources\StreamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStream extends CreateRecord
{
    protected static string $resource = StreamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
