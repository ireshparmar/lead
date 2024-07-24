<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;

class LeadErrorExport implements FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    protected $failures;
    protected $attributes;
    protected $columnNames;


    public function __construct(array $failures, array $attributes, array $columnNames)
    {
        $this->failures = $failures;
        $this->attributes = $attributes;
        $this->columnNames = $columnNames;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->failures as $failure) {
            $row = ['Row' => $failure->row()]; // Initialize with row number

            foreach ($this->attributes as $attribute) {
                $value = $failure->values()[$attribute] ?? ''; // Get the value or default to empty
                if ($attribute === $failure->attribute()) {
                    $value .= ' - ' . implode(', ', $failure->errors()); // Append errors if in the same column
                }
                $row[$this->columnNames[$attribute] ?? $attribute] = $value; // Use defined column name
            }

            $data[] = $row;
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return array_merge(['Row'], array_values($this->columnNames)); // 'Row' first, then the column names
    }



    public function title(): string
    {
        return 'Validation Errors';
    }
}
