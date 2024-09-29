<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $data = [
        //     ['name' => 'Australia', 'country_short_name' => 'AU', 'country_phone_code' => '61', 'country_currency_code' => 'AUD', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Canada', 'country_short_name' => 'CA', 'country_phone_code' => '1', 'country_currency_code' => 'CAD', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Croatia', 'country_short_name' => 'HR', 'country_phone_code' => '385', 'country_currency_code' => 'HRK', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Finland', 'country_short_name' => 'FI', 'country_phone_code' => '358', 'country_currency_code' => 'EUR', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Germany', 'country_short_name' => 'DE', 'country_phone_code' => '49', 'country_currency_code' => 'EUR', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Hungary', 'country_short_name' => 'HU', 'country_phone_code' => '36', 'country_currency_code' => 'HUF', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Latvia', 'country_short_name' => 'LV', 'country_phone_code' => '371', 'country_currency_code' => 'EUR', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Lithuania', 'country_short_name' => 'LT', 'country_phone_code' => '370', 'country_currency_code' => 'EUR', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Poland', 'country_short_name' => 'PL', 'country_phone_code' => '48', 'country_currency_code' => 'PLN', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'Russia', 'country_short_name' => 'RU', 'country_phone_code' => '7', 'country_currency_code' => 'RUB', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'United Kingdom', 'country_short_name' => 'GB', 'country_phone_code' => '44', 'country_currency_code' => 'GBP', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'United States', 'country_short_name' => 'US', 'country_phone_code' => '1', 'country_currency_code' => 'USD', 'created_at' => Carbon::now(), 'created_by' => 1],
        //     ['name' => 'India', 'country_short_name' => 'IND', 'country_phone_code' => '91', 'country_currency_code' => 'INR', 'created_at' => Carbon::now(), 'created_by' => 1],
        // ];
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('countries')->truncate();
        // DB::table('countries')->insert($data);
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

         // Path to the SQL files
         $path = database_path('sql/countries.sql');

        if (!File::exists($path)) {
            Log::error("SQL file not found: $path");
            return;
        }

        // Read the SQL file
        $sql = File::get($path);

        try {
            // Split the SQL file into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));

           // DB::beginTransaction();
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    DB::statement($statement);
                }
            }
            //DB::commit();

            Log::info("SQL file executed successfully: $path");
        } catch (\Exception $e) {
            //DB::rollBack();
            Log::error("Error executing SQL file: " . $e->getMessage());
            throw $e;
        }
    }
}
