<?php

namespace App\Filament\Resources\InquirySourceResource\Pages;

use App\Filament\Resources\InquirySourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInquirySource extends EditRecord
{
    protected static string $resource = InquirySourceResource::class;

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
