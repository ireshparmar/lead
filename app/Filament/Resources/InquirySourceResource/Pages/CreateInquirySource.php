<?php

namespace App\Filament\Resources\InquirySourceResource\Pages;

use App\Filament\Resources\InquirySourceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInquirySource extends CreateRecord
{
    protected static string $resource = InquirySourceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
