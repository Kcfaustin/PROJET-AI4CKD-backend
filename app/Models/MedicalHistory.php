<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medical_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'diabetes',
        'hypertension',
        'heart_disease',
        'liver_disease',
        'autoimmune_disease',
        'smoking_status',
        'bmi_status',
        'alcohol_consumption',
        'sedentary',
        'other_factors',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'diabetes' => 'boolean',
        'hypertension' => 'boolean',
        'heart_disease' => 'boolean',
        'liver_disease' => 'boolean',
        'autoimmune_disease' => 'boolean',
        'sedentary' => 'boolean',
    ];

    /**
     * Get the patient associated with this medical history.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
