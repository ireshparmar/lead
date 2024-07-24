<?php

namespace App\Livewire;

use App\Imports\LeadsImport;
Use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


class LeadImport extends Component
{

    use WithFileUploads;

    public $file;
    public $headers = [];
    public $columnMapping = [];

    public function updatedFile()
    {
        if ($this->file) {
            $path = $this->file->getRealPath();
            $data = Excel::toArray([], $path);
            $this->headers = isset($data[0][0]) ? array_keys($data[0][0]) : [];
            $this->updateColumnMapping();
        }
    }

    public function updateColumnMapping()
    {
        $this->columnMapping = [];
        foreach ($this->headers as $header) {
            $this->columnMapping[$header] = ''; // Initialize with empty value or provide default mapping
        }
    }

    public function import()
    {
        $path = $this->file->getRealPath();
        Excel::import(new \App\Imports\LeadsImport($this->columnMapping), $path);

        session()->flash('message', 'Data imported successfully!');
    }

    public function render()
    {
        return view('livewire.lead-import', [
            'headers' => $this->headers,
        ]);
    }
}
