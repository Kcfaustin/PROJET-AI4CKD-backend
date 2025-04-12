<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingTest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imaging_tests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'has_ultrasound',
        'ultrasound_date',
        'ultrasound_results',
        'has_ct_scan',
        'ct_scan_date',
        'ct_scan_results',
        'has_mri',
        'mri_date',
        'mri_results',
        'has_other_imaging',
        'other_imaging_type',
        'other_imaging_date',
        'other_imaging_results',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_ultrasound' => 'boolean',
        'ultrasound_date' => 'date',
        'has_ct_scan' => 'boolean',
        'ct_scan_date' => 'date',
        'has_mri' => 'boolean',
        'mri_date' => 'date',
        'has_other_imaging' => 'boolean',
        'other_imaging_date' => 'date',
    ];

    /**
     * Get the patient associated with these imaging tests.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
