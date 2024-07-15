<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RevenueLineChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Agent');
    }

    public function widgets(): array
    {
        return [
            RevenueLineChart::class,
            // other widgets
        ];
    }
}
