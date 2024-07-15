<?php
namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Lead;
use App\Models\LeadPayment;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Widgets\BarChartWidget;
use Filament\Widgets\LineChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;



class RevenueChart extends ApexChartWidget
{
    protected static ?string $heading = 'Revenue & Expenses';


    public $startDate;
    public $endDate;

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Admin');
    }


    protected function getFormSchema(): array
    {
        $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d'); // Default to last year
        $this->endDate = now()->format('Y-m-d');

        return [
            DatePicker::make('startDate')
                ->label('Start Date')
                ->default($this->startDate),
            DatePicker::make('endDate')
                ->label('End Date')
                ->default($this->endDate),
        ];
    }

    protected function getOptions(): array
    {

        $dateStart = $this->filterFormData['startDate'];
        $dateEnd = $this->filterFormData['endDate'];


        $leadPayments = LeadPayment::selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as revenue')
            ->whereBetween('payment_date', [$dateStart, $dateEnd])
            ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m")'))
            ->pluck('revenue', 'month')
            ->toArray();

        $expenses = Expense::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as expense')
            ->whereBetween('date', [$dateStart, $dateEnd])
            ->groupBy(DB::raw('DATE_FORMAT(date, "%Y-%m")'))
            ->pluck('expense', 'month')
            ->toArray();

        $months = collect(array_keys($leadPayments))
            ->merge(array_keys($expenses))
            ->unique()
            ->sort()
            ->values();

        $monthNames = $months->map(function ($month) {
                return date('F Y', strtotime($month . '-01'));
        });

        $revenueData = $months->mapWithKeys(function ($month) use ($leadPayments) {
            return [$month => $leadPayments[$month] ?? 0];
        });

        $expenseData = $months->mapWithKeys(function ($month) use ($expenses) {
            return [$month => $expenses[$month] ?? 0];
        });


        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                    'tools' => [
                        'download' => false
                    ]
                ]
            ],
            'series' => [
                [
                    'name' => 'Revenue',
                    'data' => $revenueData->values()->all(),
                ],
                [
                    'name' => 'Expenses',
                    'data' => $expenseData->values()->all(),
                ],
            ],
            'xaxis' => [
                'categories' => $monthNames->all(),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],

            'colors' => ['#90ee7e','#FF0000'],
            'stroke' => [
                'curve' => 'smooth',
            ],


        ];

    }


    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate'])) {
            $this->updateOptions();
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function updateOptions(): void
    {
        if ($this->options !== $this->getOptions()) {

            $this->options = $this->getOptions();

            if (!$this->dropdownOpen) {
                $this
                    ->dispatch('updateOptions', options: $this->options)
                    ->self();
            }
        }
    }

    public function rendering(): void
    {
        $this->updateOptions();
    }
}




