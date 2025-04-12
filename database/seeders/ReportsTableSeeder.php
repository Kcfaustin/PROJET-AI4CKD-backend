<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reports')->insert([
            [
                'title' => 'Rapport mensuel des patients',
                'type' => 'Mensuel',
                'category' => 'Patients',
                'date' => '31/03/2025',
                'author' => 'Dr. Richard',
                'status' => 'Généré',
                'format' => 'PDF',
            ],
            [
                'title' => 'Suivi trimestriel des traitements',
                'type' => 'Trimestriel',
                'category' => 'Traitements',
                'date' => '15/03/2025',
                'author' => 'Dr. Martin',
                'status' => 'Généré',
                'format' => 'Excel',
            ],
            [
                'title' => 'Analyse annuelle des médicaments',
                'type' => 'Annuel',
                'category' => 'Médicaments',
                'date' => '01/01/2025',
                'author' => 'Dr. Legrand',
                'status' => 'Généré',
                'format' => 'PDF',
            ],
            [
                'title' => 'Rapport de performance Q1',
                'type' => 'Trimestriel',
                'category' => 'Performance',
                'date' => '31/03/2025',
                'author' => 'Dr. Richard',
                'status' => 'En cours',
                'format' => 'PDF',
            ],
            [
                'title' => 'Suivi des patients à risque',
                'type' => 'Personnalisé',
                'category' => 'Patients',
                'date' => '20/03/2025',
                'author' => 'Dr. Dubois',
                'status' => 'Généré',
                'format' => 'Excel',
            ],
            [
                'title' => 'Rapport mensuel d\'avril',
                'type' => 'Mensuel',
                'category' => 'Patients',
                'date' => '30/04/2025',
                'author' => 'Dr. Richard',
                'status' => 'Programmé',
                'format' => 'PDF',
            ],
        ]);
    }
}
