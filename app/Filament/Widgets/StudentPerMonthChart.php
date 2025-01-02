<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class StudentPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Students Enrollment Per Month';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $query = Student::selectRaw('MONTH(enrollment_date) as month, COUNT(*) as count')
            ->whereYear('enrollment_date', date('Y'))
            ->groupBy('month');



        $studentsCount = $query->get()
            ->keyBy('month')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $studentsCount[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Students enrolled (' . date('Y') . ')',
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
