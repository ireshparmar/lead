<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        if(auth()->user()->hasRole('Agent')){
            $data['agent_id'] = auth()->id();
        }
        return $data;
    }

}
