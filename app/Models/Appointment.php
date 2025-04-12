<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'user_id',
        'date',          // Ajouté pour stocker la date au format 'dd/mm/YYYY'
        'time',          // Ajouté pour stocker l'heure au format 'HH:MM'
        'duration',       // Ajouté pour stocker la durée (ex: '30 min')
        'type',           // Ajouté pour stocker le type (ex: 'Consultation')
        'status',         // Modifié pour utiliser les statuts français
        'notes',
    ];

    /**
     * Statuts possibles des rendez-vous.
     */
    public const STATUSES = [
        'Planifié' => 'Planifié',
        'Confirmé' => 'Confirmé',
        'Annulé' => 'Annulé',
        'Terminé' => 'Terminé',
    ];

    /**
     * Types de rendez-vous possibles.
     */
    public const TYPES = [
        'Consultation' => 'Consultation',
        'Dialyse' => 'Dialyse',
        'Suivi' => 'Suivi',
    ];

    /**
     * Durées possibles des rendez-vous.
     */
    public const DURATIONS = [
        '30 min' => '30 min',
        '45 min' => '45 min',
        '60 min' => '60 min',
        '90 min' => '90 min',
    ];

    /**
     * Get the patient that owns this appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Convertit la date et l'heure en objet DateTime.
     */
    public function getScheduledAtAttribute(): ?\DateTime
    {
        if ($this->date && $this->time) {
            return \DateTime::createFromFormat('d/m/Y H:i', "{$this->date} {$this->time}");
        }
        return null;
    }
}
