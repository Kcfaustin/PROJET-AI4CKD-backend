<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KidneyDiseaseStage;

class KidneyDiseaseStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'stage' => 0,
                'name' => 'Normal',
                'description' => 'Fonction rénale normale. DFG ≥ 90 mL/min/1,73m² sans lésion rénale.',
                'gfr_min' => 90,
                'gfr_max' => 999,
                'recommendations' => 'Maintenir un mode de vie sain. Contrôles réguliers si facteurs de risque présents.'
            ],
            [
                'stage' => 1,
                'name' => 'Lésion rénale avec DFG normal ou augmenté',
                'description' => 'Lésion rénale avec DFG normal ou augmenté (≥90 mL/min/1,73m²). Généralement asymptomatique.',
                'gfr_min' => 90,
                'gfr_max' => 999,
                'recommendations' => 'Suivi médical annuel. Contrôle des facteurs de risque cardiovasculaires.'
            ],
            [
                'stage' => 2,
                'name' => 'Insuffisance rénale légère',
                'description' => 'Lésion rénale avec légère diminution du DFG (60-89 mL/min/1,73m²). Peu de symptômes.',
                'gfr_min' => 60,
                'gfr_max' => 89,
                'recommendations' => 'Suivi médical semestriel. Éviter les AINS et autres médicaments néphrotoxiques. Ajustement du régime alimentaire.'
            ],
            [
                'stage' => 3,
                'name' => 'Insuffisance rénale modérée',
                'description' => 'Diminution modérée du DFG (30-59 mL/min/1,73m²). Complications possibles comme l\'hypertension, l\'anémie.',
                'gfr_min' => 30,
                'gfr_max' => 59,
                'recommendations' => 'Suivi médical trimestriel. Consultation avec un néphrologue. Régime alimentaire spécifique. Traitement de l\'anémie si présente.'
            ],
            [
                'stage' => 4,
                'name' => 'Insuffisance rénale sévère',
                'description' => 'Diminution sévère du DFG (15-29 mL/min/1,73m²). Symptômes plus prononcés, préparation à la thérapie de remplacement rénal.',
                'gfr_min' => 15,
                'gfr_max' => 29,
                'recommendations' => 'Suivi médical mensuel. Préparation à la thérapie de remplacement rénal. Régime alimentaire strict. Traitement des complications.'
            ],
            [
                'stage' => 5,
                'name' => 'Insuffisance rénale terminale',
                'description' => 'Insuffisance rénale terminale (DFG <15 mL/min/1,73m²). Dialyse ou transplantation nécessaire.',
                'gfr_min' => 0,
                'gfr_max' => 14,
                'recommendations' => 'Dialyse ou transplantation rénale. Suivi médical très régulier. Régime alimentaire très strict. Gestion des complications.'
            ],
        ];

        foreach ($stages as $stage) {
            KidneyDiseaseStage::create($stage);
        }
    }
}
