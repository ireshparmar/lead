<?php

namespace App\Filament\Resources\InquirySourceResource\Pages;

use App\Filament\Resources\InquirySourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInquirySources extends ListRecords
{
    protected static string $resource = InquirySourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
