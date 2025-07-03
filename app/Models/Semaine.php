<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semaine extends Model
{
    use HasFactory;

    protected $fillable = ['numero_semaine', 'date_debut', 'date_fin', 'annee_scolaire_id','etablissement_id'];

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function feries()
    {
        return $this->belongsToMany(Ferie::class, 'sem_fers');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
