<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'adresse', 'telephone', 'directeur_etablissement_id','complexe_id'];

    public function directeurEtablissement()
    {
        return $this->belongsTo(DirecteurEtablissement::class, 'directeur_etablissement_id');
    }
    public function complexe()
    {
        return $this->belongsTo(Complexe::class);
    }

    public function formateurs()
    {
        return $this->hasMany(Formateur::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    public function salles()
    {
        return $this->hasMany(Salle::class);
    }

    public function anneesScolaires()
    {
        return $this->hasMany(AnneeScolaire::class);
    }
    
    public function semaines()
    {
        return $this->hasMany(Semaine::class);
    }
    public function secteurs()
    {
        return $this->belongsToMany(Secteur::class, 'sect_efps');
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'offrirs');
    }
}
