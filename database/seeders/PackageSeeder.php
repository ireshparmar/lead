<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            ['package_type' => 'Student', 'package_name' => 'Registration Fee', 'amount' => 25000.00, 'remark' => '', 'created_by' => 1],
            ['package_type' => 'Student', 'package_name' => 'IELTS Fees', 'amount' => 15000.00, 'remark' => '', 'created_by' => 1],
            ['package_type' => 'Student', 'package_name' => 'PTE Coaching', 'amount' => 15000.00, 'remark' => '', 'created_by' => 1],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
