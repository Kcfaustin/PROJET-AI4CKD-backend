<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'first_name' => 'Moussa',
                'last_name' => 'Fall',
                'email' => 'm.fall@patient.com',
                'phone' => '+221 77 200 00 01',
                'password' => Hash::make('Fall123!'),
                'role' => 'patient',
                'status' => 'actif',
                'license_number' => null,
                'specialization' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Aïssatou',
                'last_name' => 'Diallo',
                'email' => 'a.diallo@patient.com',
                'phone' => '+221 77 200 00 02',
                'password' => Hash::make('Diallo123!'),
                'role' => 'patient',
                'status' => 'actif',
                'license_number' => null,
                'specialization' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Papa',
                'last_name' => 'Sène',
                'email' => 'p.sene@patient.com',
                'phone' => '+221 77 200 00 03',
                'password' => Hash::make('Sene123!'),
                'role' => 'patient',
                'status' => 'actif',
                'license_number' => null,
                'specialization' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Rokhaya',
                'last_name' => 'Gueye',
                'email' => 'r.gueye@patient.com',
                'phone' => '+221 77 200 00 04',
                'password' => Hash::make('Gueye123!'),
                'role' => 'patient',
                'status' => 'actif',
                'license_number' => null,
                'specialization' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($patients as $userData) {
            $user = User::create($userData);

            // Création du patient associé dans la table patients
            DB::table('patients')->insert([
                'identifiant' => 'PAT-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'birth_date' => now()->subYears(rand(18, 70))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'address' => 'Adresse ' . $user->last_name,
                'phone' => $user->phone,
                'status' => ['Stable', 'Attention', 'Critique'][array_rand(['Stable', 'Attention', 'Critique'])], // Correction ici
                'emergency_contact' => 'Contact ' . $user->last_name,
                'referring_doctor_id' => User::where('role', 'medecin')->inRandomOrder()->first()->id,
                'photo_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('4 patients utilisateurs et enregistrements patients créés avec succès');
    }
}
