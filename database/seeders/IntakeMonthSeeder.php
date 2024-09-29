<?php

namespace Database\Seeders;

use App\Models\Intakemonth;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntakeMonthSeeder extends Seeder
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
                'inmonth_name' => "Jan/Feb/Mar",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'inmonth_name' => "June/July/Aug",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'inmonth_name' => "Oct/Nov/Dec",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
        ];

        $dataCnt = DB::table('intakemonths')->count();
        if ($dataCnt == 0) {
            Intakemonth::insert($data);
        }
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
