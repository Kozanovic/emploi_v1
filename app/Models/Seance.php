<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_seance',
        'heure_debut',
        'heure_fin',
        'type',
        'duree',
        'numero_seance',
        'semaine_id',
        'salle_id',
        'module_id',
        'formateur_id',
        'groupe_id'
    ];

    public function semaine()
    {
        return $this->belongsTo(Semaine::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
