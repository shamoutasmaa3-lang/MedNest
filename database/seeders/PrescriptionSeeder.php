<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Medicine;

class PrescriptionSeeder extends Seeder
{
    public function run()
    {
        $doctor = User::where('role', 'doctor')->first();
        $patient = User::where('role', 'patient')->first();

        if (!$doctor) {
            $this->command->warn('Skipping PrescriptionSeeder: No doctor found. Run UserSeeder first.');
            return;
        }

        if (!$patient) {
            $this->command->warn('Skipping PrescriptionSeeder: No patient found. Patients can be registered via the API.');
            return;
        }

        $prescription = Prescription::create([
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'status'     => 'pending',
        ]);

        $medicine1 = Medicine::find(1);
        $medicine2 = Medicine::find(2);

        if ($medicine1 && $medicine2) {
            $prescription->medicines()->attach($medicine1->id, ['dosage' => '200mg', 'duration' => '5 days']);
            $prescription->medicines()->attach($medicine2->id, ['dosage' => '5mg', 'duration' => '10 days']);
            $this->command->info('Test prescription with medicines created.');
        } else {
            $this->command->warn('Prescription created but no medicines found (IDs 1 and 2 missing).');
        }
    }
}
