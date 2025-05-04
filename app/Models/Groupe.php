<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'annee', 'filiere_id', 'etablissement_id'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'affectations')
            ->withPivot('formateur_id');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    public function suivres()
    {
        return $this->hasMany(Suivre::class);
    }
}
