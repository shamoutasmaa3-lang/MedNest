<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Patient
        User::create([
            'name' => 'Sara Patient',
            'email' => 'patient@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'address' => 'Damascus',
        ]);

        // Doctor
        User::create([
            'name' => 'Dr. Ahmad Doctor',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'address' => 'Damascus',
        ]);

        // Pharmacist
        User::create([
            'name' => 'Mohammad Pharmacist',
            'email' => 'pharmacist@example.com',
            'password' => Hash::make('password'),
            'role' => 'pharmacist',
            'address' => 'Damascus',
        ]);

        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'address' => 'Damascus',
        ]);

        // Delivery (مندوب توصيل)
        User::create([
            'name' => 'Khalid Delivery',
            'email' => 'delivery@example.com',
            'password' => Hash::make('password'),
            'role' => 'delivery',
            'address' => 'Damascus',
        ]);
    }
}