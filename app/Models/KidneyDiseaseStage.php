<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KidneyDiseaseStage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'stage',
        'gfr_range',
        'description',
        'recommendations',
    ];

    /**
     * Get the patients at this kidney disease stage.
     */
    public function patients()
    {
        return $this->hasMany(PatientRecord::class, 'kidney_disease_stage_id');
    }
}
