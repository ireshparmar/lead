<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class StudentProgress extends Component
{
    public $studentId;

    public $applicationDate;
    public $admissionDate;
    public $visaDate;
    public $enrollmentDate;

    public function mount($studentId)
    {
        $this->studentId = $studentId;
        $student = Student::where('id', $studentId)->first();

        $this->enrollmentDate = $student->enrollment_date;
    }

    public function render()
    {
        return view('livewire.student-progress', ['enrollmentDate' => $this->enrollmentDate]);
    }
}
