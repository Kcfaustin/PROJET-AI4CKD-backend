<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyzeController extends Controller
{
    /**
     * Analyse les données pour détecter la maladie rénale chronique.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyze(Request $request): JsonResponse
    {
        // Validation des données d'entrée
        $validated = $request->validate([
            'age' => 'required|numeric',
            'blood_pressure' => 'required|numeric',
            'specific_gravity' => 'required|numeric',
            'albumin' => 'required|numeric',
            'sugar' => 'required|numeric',
            'red_blood_cells' => 'required|string|in:normal,abnormal',
            'pus_cell' => 'required|string|in:normal,abnormal',
            'pus_cell_clumps' => 'required|string|in:present,notpresent',
            'bacteria' => 'required|string|in:present,notpresent',
            'blood_glucose_random' => 'required|numeric',
            'blood_urea' => 'required|numeric',
            'serum_creatinine' => 'required|numeric',
            'sodium' => 'required|numeric',
            'potassium' => 'required|numeric',
            'hemoglobin' => 'required|numeric',
            'packed_cell_volume' => 'required|numeric',
            'white_blood_cell_count' => 'required|numeric',
            'red_blood_cell_count' => 'required|numeric',
            'hypertension' => 'required|string|in:yes,no',
            'diabetes_mellitus' => 'required|string|in:yes,no',
            'coronary_artery_disease' => 'required|string|in:yes,no',
            'appetite' => 'required|string|in:good,poor',
            'pedal_edema' => 'required|string|in:yes,no',
            'anemia' => 'required|string|in:yes,no',
        ]);

        // Logique d'analyse (à implémenter avec un modèle ML réel)
        // Pour l'instant, nous utilisons une logique simplifiée basée sur quelques facteurs clés
        
        $stage = $this->determineKidneyDiseaseStage($validated);
        
        return response()->json([
            'success' => true,
            'data' => [
                'has_disease' => $stage > 0,
                'stage' => $stage,
                'description' => $this->getStageDescription($stage),
                'recommendations' => $this->getRecommendations($stage),
                'avatar_model' => "kidney_stage_{$stage}.glb" // Nom du fichier 3D pour ce stade
            ]
        ]);
    }
    
    /**
     * Détermine le stade de la maladie rénale chronique.
     * 
     * @param array $data
     * @return int
     */
    private function determineKidneyDiseaseStage(array $data): int
    {
        // Facteurs de risque majeurs
        $riskFactors = 0;
        
        // Créatinine sérique élevée (indicateur majeur)
        if ($data['serum_creatinine'] > 1.5) {
            $riskFactors += 2;
        }
        
        // Albumine dans l'urine (protéinurie)
        if ($data['albumin'] > 0) {
            $riskFactors += $data['albumin'];
        }
        
        // Hypertension
        if ($data['hypertension'] === 'yes') {
            $riskFactors += 1;
        }
        
        // Diabète
        if ($data['diabetes_mellitus'] === 'yes') {
            $riskFactors += 1;
        }
        
        // Anémie
        if ($data['anemia'] === 'yes' || $data['hemoglobin'] < 11) {
            $riskFactors += 1;
        }
        
        // Œdème
        if ($data['pedal_edema'] === 'yes') {
            $riskFactors += 1;
        }
        
        // Détermination du stade
        if ($riskFactors === 0) {
            return 0; // Pas de maladie
        } elseif ($riskFactors <= 2) {
            return 1; // Stade 1
        } elseif ($riskFactors <= 4) {
            return 2; // Stade 2
        } elseif ($riskFactors <= 6) {
            return 3; // Stade 3
        } elseif ($riskFactors <= 8) {
            return 4; // Stade 4
        } else {
            return 5; // Stade 5
        }
    }
    
    /**
     * Obtient la description du stade de la maladie.
     * 
     * @param int $stage
     * @return string
     */
    private function getStageDescription(int $stage): string
    {
        switch ($stage) {
            case 0:
                return "Pas de maladie rénale détectée. Fonction rénale normale.";
            case 1:
                return "Stade 1: Lésion rénale avec DFG normal ou augmenté (≥90 mL/min/1,73m²). Généralement asymptomatique.";
            case 2:
                return "Stade 2: Lésion rénale avec légère diminution du DFG (60-89 mL/min/1,73m²). Peu de symptômes.";
            case 3:
                return "Stade 3: Diminution modérée du DFG (30-59 mL/min/1,73m²). Complications possibles comme l'hypertension, l'anémie.";
            case 4:
                return "Stade 4: Diminution sévère du DFG (15-29 mL/min/1,73m²). Symptômes plus prononcés, préparation à la thérapie de remplacement rénal.";
            case 5:
                return "Stade 5: Insuffisance rénale terminale (DFG <15 mL/min/1,73m²). Dialyse ou transplantation nécessaire.";
            default:
                return "Stade indéterminé.";
        }
    }
    
    /**
     * Obtient les recommandations basées sur le stade de la maladie.
     * 
     * @param int $stage
     * @return array
     */
    private function getRecommendations(int $stage): array
    {
        $common = [
            "Maintenir une alimentation équilibrée, faible en sel",
            "Rester bien hydraté",
            "Éviter les médicaments néphrotoxiques",
            "Contrôler la pression artérielle et la glycémie"
        ];
        
        switch ($stage) {
            case 0:
                return [
                    "Maintenir un mode de vie sain",
                    "Contrôles réguliers si facteurs de risque présents"
                ];
            case 1:
                return array_merge($common, [
                    "Suivi médical annuel",
                    "Contrôle des facteurs de risque cardiovasculaires"
                ]);
            case 2:
                return array_merge($common, [
                    "Suivi médical semestriel",
                    "Éviter les AINS et autres médicaments néphrotoxiques",
                    "Ajustement du régime alimentaire"
                ]);
            case 3:
                return array_merge($common, [
                    "Suivi médical trimestriel",
                    "Consultation avec un néphrologue",
                    "Régime alimentaire spécifique",
                    "Traitement de l'anémie si présente"
                ]);
            case 4:
                return array_merge($common, [
                    "Suivi médical mensuel",
                    "Préparation à la thérapie de remplacement rénal",
                    "Régime alimentaire strict",
                    "Traitement des complications"
                ]);
            case 5:
                return [
                    "Dialyse ou transplantation rénale",
                    "Suivi médical très régulier",
                    "Régime alimentaire très strict",
                    "Gestion des complications"
                ];
            default:
                return $common;
        }
    }
}
