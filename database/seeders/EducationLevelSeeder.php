<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Data to be seeded
        $educationLevels = [
            [
                'name' => '10th',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => '12th',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'Diploma',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'Graduate',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'Post Graduate',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => 'PhD',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
        ];

        // Insert data
        DB::table('education_levels')->insert($educationLevels);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
