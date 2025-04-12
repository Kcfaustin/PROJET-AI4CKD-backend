<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('appointments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $doctorIds = DB::table('users')
                     ->where('role', 'medecin')
                     ->pluck('id')
                     ->toArray();

        $patientIds = DB::table('patients')->pluck('id')->toArray();

        if (empty($doctorIds)) {
            $this->call(DoctorSeeder::class);
            $doctorIds = DB::table('users')
                         ->where('role', 'medecin')
                         ->pluck('id')
                         ->toArray();
        }

        if (empty($patientIds)) {
            $this->call(PatientSeeder::class);
            $patientIds = DB::table('patients')->pluck('id')->toArray();
        }

        $types = ['Consultation', 'Dialyse', 'Suivi', 'Examen', 'Contrôle'];
        $statuses = ['Planifié', 'Confirmé', 'Annulé', 'Terminé'];
        $durations = ['30 min', '45 min', '60 min', '90 min'];

        $appointments = [];

        for ($i = 0; $i < 50; $i++) {
            $dateTime = Carbon::today()
                ->addDays(rand(0, 90))
                ->setTime(rand(8, 17), rand(0, 1) * 30, 0);

            $appointments[] = [
                'patient_id' => $patientIds[array_rand($patientIds)],
                'user_id' => $doctorIds[array_rand($doctorIds)],
                'date' => $dateTime->format('Y-m-d'), // Séparer en date
                'time' => $dateTime->format('H:i'),   // Séparer en heure
                'duration' => $durations[array_rand($durations)],
                'type' => $types[array_rand($types)],
                'status' => $statuses[array_rand($statuses)],
                'notes' => rand(0, 1) ? 'Notes supplémentaires pour le rendez-vous ' . ($i + 1) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('appointments')->insert($appointments);

        $this->command->info('Table appointments seeded with ' . count($appointments) . ' records.');
        $this->command->info('Médecins utilisés: ' . count(array_unique(array_column($appointments, 'user_id'))) . '/' . count($doctorIds));
        $this->command->info('Patients utilisés: ' . count(array_unique(array_column($appointments, 'patient_id'))) . '/' . count($patientIds));
    }
}
