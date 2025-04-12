<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medical_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'user_id',
        'kidney_disease_stage_id',
        'diagnosis_date',
        'notes',
        'on_dialysis',
        'dialysis_start_date',
        'current_treatment',
        'blood_type',
        'creatinine_level',
        'gfr',
        'albuminuria',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'potassium_level',
        'hemoglobin_level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'diagnosis_date' => 'date',
        'dialysis_start_date' => 'date',
        'on_dialysis' => 'boolean',
        'creatinine_level' => 'float',
        'gfr' => 'float',
        'albuminuria' => 'float',
        'blood_pressure_systolic' => 'integer',
        'blood_pressure_diastolic' => 'integer',
        'potassium_level' => 'float',
        'hemoglobin_level' => 'float',
    ];

    /**
     * Get the patient associated with this medical info.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor (user) who created this record.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the kidney disease stage associated with this record.
     */
    public function kidneyDiseaseStage(): BelongsTo
    {
        return $this->belongsTo(KidneyDiseaseStage::class);
    }

    /**
     * Helper method to get formatted blood pressure.
     */
    public function getBloodPressureAttribute(): ?string
    {
        return $this->blood_pressure_systolic && $this->blood_pressure_diastolic
            ? "{$this->blood_pressure_systolic}/{$this->blood_pressure_diastolic}"
            : null;
    }
}
