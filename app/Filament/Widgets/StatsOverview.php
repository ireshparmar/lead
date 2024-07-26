<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Leads', Lead::count())
            ->color('primary'),
            Stat::make('Total Revenue', LeadPayment::sum('amount'))->color('gray'),
            Stat::make('Total Expenses', Expense::sum('amount'))->color('gray'),
            Stat::make('Total Agents', User::role('Agent')->count())->color('info'),
        ];
    }

}
