<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Student'],
            ['name' => 'Visitor'],
            ['name' => 'Work Permit'],
        ];

        DB::table('visa_types')->truncate();
        DB::table('visa_types')->insert($types);
    }
}
