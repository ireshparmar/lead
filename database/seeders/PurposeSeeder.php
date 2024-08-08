<?php

namespace Database\Seeders;

use App\Models\Purpose;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'purpose_name' => "Study Abroad",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_name' => "Personal Counselling",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_name' => "Student PR",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_name' => "Coaching",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_name' => "Work Permit",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],

        ];

        $dataCnt = DB::table('purposes')->count();
        if($dataCnt == 0) {
            Purpose::insert($data);
        }
    }
}
