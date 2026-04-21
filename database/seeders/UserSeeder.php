<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
   
    public function run()
    {
       
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');
        $adminName = env('ADMIN_NAME', 'Administrator');
        $adminPhone = env('ADMIN_PHONE', '0990000000');
        $adminAddress = env('ADMIN_ADDRESS', 'Syria');

        
        $existingAdmin = User::where('email', $adminEmail)->first();

        if (!$existingAdmin) {
            User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'phone' => $adminPhone,
                'role' => 'admin',
                'addresss' => $adminAddress,
            ]);

            $this->command->info('Admin user created successfully.');
        } else {
           
            $existingAdmin->update([
                'name' => $adminName,
                'phone' => $adminPhone,
                'addresss' => $adminAddress,
                'role' => 'admin',
            ]);
            
            
            if (!Hash::check($adminPassword, $existingAdmin->password)) {
                $existingAdmin->password = Hash::make($adminPassword);
                $existingAdmin->save();
                $this->command->info('Admin password updated.');
            }

            $this->command->info('Admin user already exists. Synced with .env settings.');
        }
    }
}