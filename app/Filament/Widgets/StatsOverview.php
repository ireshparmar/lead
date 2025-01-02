<?php

namespace App\Filament\Widgets;

use App\Helpers\CurrencyHelper;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    //protected static ?string $pollingInterval = '15s';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    protected function getStats(): array
    {
        $invoices = Invoice::get();
        $leadCommission = $studentCommission = $leadAgentCommission = $studentAgentCommission = 0;
        if (!$invoices->isEmpty()) {
            foreach ($invoices as $commission) {
                if ($commission['status'] == 'Commission Received') {
                    $ownCommission = CurrencyHelper::convert($commission['own_commission'], $commission['payment_currency'], $commission['base_currency'], $commission['base_currency_rate']);

                    if ($commission->invoice_type == 'lead') {
                        $leadCommission = $leadCommission + $ownCommission;
                    } else {
                        $studentCommission = $studentCommission + $ownCommission;
                    }
                }
                if ($commission['agent_payment_status'] == 'Commission Paid') {
                    $agentCommission = CurrencyHelper::convert($commission['agent_commission'], $commission['payment_currency'], $commission['base_currency'], $commission['base_currency_rate']);
                    if ($commission->invoice_type == 'lead') {
                        $leadAgentCommission = $leadAgentCommission + $agentCommission;
                    } else {
                        $studentAgentCommission = $studentAgentCommission + $agentCommission;
                    }
                }
            }
            $leadCommission = CurrencyHelper::formatAmount($leadCommission);
            $studentCommission = CurrencyHelper::formatAmount($studentCommission);
            $leadAgentCommission = CurrencyHelper::formatAmount($leadAgentCommission);
            $studentAgentCommission = CurrencyHelper::formatAmount($studentAgentCommission);
        }

        return [
            Stat::make('Total Leads', Lead::count())
                ->color('primary'),
            Stat::make('Total Students', Student::count())
                ->color('primary'),
            Stat::make('Total Revenue', LeadPayment::sum('amount'))->color('gray'),
            Stat::make('Total Expenses', Expense::sum('amount'))->color('gray'),
            Stat::make('Total Agents', User::role('Agent')->count())->color('info'),
            Stat::make('Lead Commissions Received (' . config('app.base_currency') . ')', $leadCommission)->url(route('filament.admin.resources.lead-invoice-managements.index', ['tableFilters' => [
                'status' => ['values' => [0 => 'Commission Received']],
            ]])),
            Stat::make('Student Comissions Received (' . config('app.base_currency') . ')', $studentCommission)->url(route('filament.admin.resources.student-invoice-managements.index', ['tableFilters' => [
                'status' => ['values' => [0 => 'Commission Received']],
            ]])),
            Stat::make('Agent Lead Commissions Paid (' . config('app.base_currency') . ')', $leadAgentCommission)->url(route('filament.admin.resources.lead-agent-commissions.index', ['tableFilters' => [
                'agent_payment_status' => ['values' => [0 => 'Commission Paid']],
            ]])),
            Stat::make('Agent Student Comissions Paid (' . config('app.base_currency') . ')', $studentAgentCommission)->url(route('filament.admin.resources.student-agent-commissions.index', ['tableFilters' => [
                'agent_payment_status' => ['values' => [0 => 'Commission Paid']],
            ]])),
        ];
    }
}
