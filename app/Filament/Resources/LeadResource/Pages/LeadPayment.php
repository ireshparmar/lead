<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Pages\Page;

class LeadPayment extends Page
{
    protected static string $resource = LeadResource::class;

    protected static string $view = 'filament.resources.lead-resource.pages.lead-payment';
}
