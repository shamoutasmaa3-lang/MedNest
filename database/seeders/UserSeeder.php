<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Doctor
        User::create([
            'name'     => 'Dr. Ahmad Doctor',
            'email'    => 'doctor@example.com',
            'password' => Hash::make('Doctor@2025!'),
            'role'     => 'doctor',
            'address'  => 'Damascus',
            'phone'    => '0992222222',
        ]);

        // Pharmacist
        User::create([
            'name'     => 'Mohammad Pharmacist',
            'email'    => 'pharmacist@example.com',
            'password' => Hash::make('Pharma@2025!'),
            'role'     => 'pharmacist',
            'address'  => 'Damascus',
            'phone'    => '0993333333',
        ]);

        

        // Delivery
        User::create([
            'name'     => 'Khalid Delivery',
            'email'    => 'delivery@example.com',
            'password' => Hash::make('Delivery@2025!'),
            'role'     => 'delivery',
            'address'  => 'Damascus',
            'phone'    => '0995555555',
        ]);
    }
}