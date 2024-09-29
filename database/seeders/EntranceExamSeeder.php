<?php

namespace Database\Seeders;

use App\Models\EnteranceExam;
use Illuminate\Database\Seeder;
use App\Models\EntranceExam;

class EntranceExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exams = [
            ['name' => 'IELTS', 'type' => 'Language', 'status' => 'Active', 'created_by' => 1],
            ['name' => 'TOEFL', 'type' => 'Language', 'status' => 'Active', 'created_by' => 1],
            ['name' => 'GRE', 'type' => 'Aptitude', 'status' => 'Active', 'created_by' => 1],
            ['name' => 'GMAT', 'type' => 'Aptitude', 'status' => 'Active', 'created_by' => 1],
        ];

        foreach ($exams as $exam) {
            EnteranceExam::create($exam);
        }
    }
}
