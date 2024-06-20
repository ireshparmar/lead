<?php

namespace App\Filament\Resources\LeadDocResource\Pages;

use App\Filament\Resources\LeadDocResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLeadDoc extends CreateRecord
{
    protected static string $resource = LeadDocResource::class;
}
