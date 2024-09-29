<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Data to be seeded
        $types = [
            [
                'name' => 'Adhar Card',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Passport',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Driving Licence',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Pcc',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Resume',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => '10th Mark Sheet',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => '12th Mark Sheet',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => '12th Credit Certificate',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => "Bachelor's Mark Sheet + Degree Certificate",
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => "Master's Mark Sheet + Degree Certificate",
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Backlog Certificate/No Backlog Certificate',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Transcript',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Letter of Recommendation',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Medium of Instruction',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'IELTS Score Card',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Compulsory',
                'module' => json_encode(['Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Appoinment Letter',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Salary Slip (Last 3 Month)',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
            [
                'name' => 'Salary Account Statement',
                'created_at' => Carbon::now(),
                'created_by' => 1,
                'type' => 'Optional',
                'module' => json_encode(['General', 'Student']), // Manually encode array to JSON
            ],
        ];

        // Insert data
        DB::table('document_types')->insert($types);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
