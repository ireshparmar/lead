<?php

namespace Database\Seeders;

use App\Models\Intakeyear;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntakeYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $data = [
            [
                'inyear_name' => "2024",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'inyear_name' => "2025",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],

        ];

        $dataCnt = DB::table('intakeyears')->count();
        if ($dataCnt == 0) {
            Intakeyear::insert($data);
        }
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
