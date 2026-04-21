<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Pharmacist
        \App\Models\User::create([
            'name' => 'Test Pharmacist',
            'email' => 'pharmacist@test.com',
            'password' => bcrypt('password'),
            'phone' => '0991112222',
            'role' => 'pharmacist',
            'addresss' => 'Damascus',
        ]);

        // Doctor
        \App\Models\User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@test.com',
            'password' => bcrypt('password'),
            'phone' => '0993334444',
            'role' => 'doctor',
            'addresss' => 'Damascus',
        ]);
    }
}
