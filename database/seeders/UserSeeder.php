<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Patient
        User::create([
            'name' => 'Sara Patient',
            'email' => 'patient@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'addresss' => 'Damascus',
        ]);

        // Doctor
        User::create([
            'name' => 'Dr. Ahmad Doctor',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'addresss' => 'Damascus',
        ]);

        // Pharmacist
        User::create([
            'name' => 'Mohammad Pharmacist',
            'email' => 'pharmacist@example.com',
            'password' => Hash::make('password'),
            'role' => 'pharmacist',
            'addresss' => 'Damascus',
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'addresss' => 'Damascus',
        ]);
    }
}