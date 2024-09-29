<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Path to the SQL files
          $path = database_path('sql/states.sql');

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
