<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KidneyDiseaseStageSeeder::class, // D'abord les tables de référence
            AdminUserSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class, // Remplacer PatientsTableSeeder
            // AppointmentsTableSeeder::class doit venir après
            AppointmentsTableSeeder::class,
            ReportsTableSeeder::class
        ]);
    }
}
