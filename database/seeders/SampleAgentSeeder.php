<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 2000; $i++) {
            $agent = User::create([
                'name' => 'Agent ' . $i,
                'email' => 'agent' . $i . '@example.com',
                'email_verified_at' => Carbon::now(),
                'status' => 'Active'
            ]);

            // Assign a role to the agent (you can modify this logic as needed)
            $agent->assignRole(2); // Example: alternate between admin and agent roles
        }

    }
}
