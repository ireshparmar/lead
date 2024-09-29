<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('roles')->truncate();

        Role::insert([
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Agent',
                'guard_name' => 'web',
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Staff',
                'guard_name' => 'web',
                'created_at' => Carbon::now()
            ],
        ]);

        $checkAdmin = DB::table('users')->where('id',1)->count();
        if($checkAdmin==0){
            DB::table('users')->insert([
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin@123')
            ]);
            DB::table('model_has_roles')->insert([
                'role_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => 1,
            ]);
        }
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
