<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Australia','created_at'  => Carbon::now()],
            ['name' => 'Canada','created_at'  => Carbon::now()],
            ['name' => 'Croatia','created_at'  => Carbon::now()],
            ['name' => 'Finland','created_at'  => Carbon::now()],
            ['name' => 'Germany','created_at'  => Carbon::now()],
            ['name' => 'Hungry','created_at'  => Carbon::now()],
            ['name' => 'Latvia','created_at'  => Carbon::now()],
            ['name' => 'Lithuanian','created_at'  => Carbon::now()],
            ['name' => 'Poland','created_at'  => Carbon::now()],
            ['name' => 'Russia','created_at'  => Carbon::now()],
            ['name' => 'UK','created_at'  => Carbon::now()],
            ['name' => 'USA','created_at'  => Carbon::now()],
        ];
        DB::table('countries')->truncate();
        DB::table('countries')->insert($data);
    }
}
