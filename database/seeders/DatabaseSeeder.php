<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use App\Models\Intakemonth;
use App\Models\Intakeyear;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(RolesSeeder::class);
        $this->call(ExpenseCategoriesSeeder::class);
        $this->call(VisaTypeSeeder::class);
        //$this->call(CountrySeeder::class);
        $this->call(InquirySourceSeeder::class);
        $this->call(IntakeYearSeeder::class);
        $this->call(IntakeMonthSeeder::class);
        $this->call(PurposeSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(EducationLevelSeeder::class);
        $this->call(DurationSeeder::class);
        $this->call(EntranceExamSeeder::class);
    }
}
