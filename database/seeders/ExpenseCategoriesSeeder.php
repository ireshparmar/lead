<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $categories = [
            ['name' => 'Travel'],
            ['name' => 'Accommodation'],
            ['name' => 'Meals'],
            ['name' => 'Office Supplies'],
            ['name' => 'Professional Services'],
            ['name' => 'Client Services'],
            ['name' => 'Maintenance'],
            ['name' => 'Utilities'],
            ['name' => 'Insurance'],
            ['name' => 'Miscellaneous'],
        ];
        DB::table('expense_categories')->truncate();
        DB::table('expense_categories')->insert($categories);
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
