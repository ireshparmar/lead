<?php

namespace Database\Seeders;

use App\Models\InquirySource;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InquirySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $sources = [
            [
                'insource_name' => "Walk In Student",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'insource_name' => "Google Search",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'insource_name' => "Referral",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
            [
                'insource_name' => "Instagram",
                'status' => 'Active',
                'created_by' => 1,
                'created_at' => Carbon::now()
            ],
        ];

        $sourcesCnt = DB::table('inquiry_sources')->count();
        if($sourcesCnt == 0) {
            InquirySource::insert($sources);
        }
    }
}
