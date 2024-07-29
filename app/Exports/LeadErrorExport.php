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

        // Iterate over each failure
foreach ($this->failures as $failure) {
    $rowIndex = $failure->row(); // Get the row number
    $columnName = $this->columnNames[$failure->attribute()] ?? $failure->attribute(); // Get the column name
    $errorMessage = implode(', ', $failure->errors()); // Get the error message
    $columnValue = $failure->values()[$failure->attribute()] ?? ''; // Get the value or default to empty

    // Initialize the row if it doesn't exist
    if (!isset($data[$rowIndex])) {
        $data[$rowIndex] = ['Row' => $rowIndex];

        // Preserve existing data in columns
        foreach ($this->attributes as $attribute) {
            $attrColumnName = $this->columnNames[$attribute] ?? $attribute;
            $data[$rowIndex][$attrColumnName] = $failure->values()[$attribute] ?? '';
        }
    }

    // Add the error to the corresponding column
    if (isset($data[$rowIndex][$columnName])) {
        // Append to existing error message if already set
        $data[$rowIndex][$columnName] .= ' - ' . $errorMessage;
    } else {
        $data[$rowIndex][$columnName] = $columnValue . ' - ' . $errorMessage;
    }
}

// Re-index the array to have a zero-based index for the rows
$data = array_values($data);
       // unset($data[0]);
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
