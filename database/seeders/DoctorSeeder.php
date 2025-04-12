<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Diop',
                'email' => 'dr.diop@mrc-app.com',
                'phone' => '+221 77 100 00 01',
                'password' => Hash::make('Diop123!'),
                'role' => 'medecin',
                'status' => 'actif',
                'license_number' => 'MED-001',
                'specialization' => 'Néphrologie',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Aminata',
                'last_name' => 'Ndiaye',
                'email' => 'dr.ndiaye@mrc-app.com',
                'phone' => '+221 77 100 00 02',
                'password' => Hash::make('Ndiaye123!'),
                'role' => 'medecin',
                'status' => 'actif',
                'license_number' => 'MED-002',
                'specialization' => 'Cardiologie',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jean',
                'last_name' => 'Gomis',
                'email' => 'dr.gomis@mrc-app.com',
                'phone' => '+221 77 100 00 03',
                'password' => Hash::make('Gomis123!'),
                'role' => 'medecin',
                'status' => 'actif',
                'license_number' => 'MED-003',
                'specialization' => 'Pédiatrie',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Fatou',
                'last_name' => 'Sarr',
                'email' => 'dr.sarr@mrc-app.com',
                'phone' => '+221 77 100 00 04',
                'password' => Hash::make('Sarr123!'),
                'role' => 'medecin',
                'status' => 'actif',
                'license_number' => 'MED-004',
                'specialization' => 'Dermatologie',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($doctors as $doctor) {
            User::create($doctor);
        }

        $this->command->info('4 médecins créés avec succès');
    }
}
