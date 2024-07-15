<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class LeadPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Leads Per Month';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $query = Lead::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
             ->groupBy('month');

        if(auth()->user()->hasRole('Agent')){
            $query->where(function ($query) {
                $query->where('created_by', auth()->user()->id)
                    ->orWhere('agent_id', auth()->user()->id);
            });
        }

        $leadsCount = $query->get()
                            ->keyBy('month')
                            ->map(function ($item) {
                                return $item->count;
                    })
                    ->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $leadsCount[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leads created',
                    'data' => $data,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
