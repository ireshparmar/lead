<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Data to be seeded
        $durations = [
            [
                'name' => '1 Year',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => '2 Year',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],
            [
                'name' => '3 Year',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],

            [
                'name' => '4 Year',
                'created_at' => Carbon::now(),
                'created_by' => 1,
            ],

        ];

        // Insert data
        DB::table('durations')->insert($durations);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
