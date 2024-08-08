<?php

namespace Database\Seeders;

use App\Models\Purpose;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $purposes = Purpose::pluck('id','purpose_name')->toArray();
            $data = [
            [
                'purpose_id'   => $purposes['Study Abroad'],
                'service_name' => "Student Visa",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Personal Counselling'],
                'service_name' => "Allied Services",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Personal Counselling'],
                'service_name' => "Visa Extension",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Student PR'],
                'service_name' => "Student PR Program Details",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Coaching'],
                'service_name' => "IELTS Coaching",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Coaching'],
                'service_name' => "GRE Coaching",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Coaching'],
                'service_name' => "Spoken English",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Work Permit'],
                'service_name' => "Australlia Work Permit",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Work Permit'],
                'service_name' => "UK Work Permit",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'purpose_id'   => $purposes['Work Permit'],
                'service_name' => "Canada Work Permit",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],

        ];

        $dataCnt = DB::table('services')->count();
        if($dataCnt == 0) {
            Service::insert($data);
        }
    }
}
