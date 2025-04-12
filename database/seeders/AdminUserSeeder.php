<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Création de l'administrateur
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Système',
            'email' => 'admin@mrc-app.com',
            'phone' => '+221 77 123 45 67',
            'password' => Hash::make('12345678'),
            'license_number' => 'MED-ADMIN-001', // Licence spécifique à l'admin
            'specialization' => 'Administration Système', // Spécialisation spécifique à l'admin
            'role' => 'admin', // Rôle d'admin
            'status' => 'actif', // Statut actif
        ]);

        // Création d'un médecin
        User::create([
            'first_name' => 'Medecin',
            'last_name' => 'User',
            'email' => 'medecin@mrc-app.com',
            'phone' => '+221 77 133 45 67',
            'password' => Hash::make('password'), // Mot de passe pour le médecin
            'license_number' => 'MED12345', // Licence spécifique au médecin
            'specialization' => 'Cardiologie', // Spécialisation du médecin
            'role' => 'medecin', // Rôle de médecin
            'status' => 'actif', // Statut actif
        ]);

        $this->command->info('Admin et médecin créés avec succès');
    }
}
