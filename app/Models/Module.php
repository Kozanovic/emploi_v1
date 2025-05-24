<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'masse_horaire_presentiel',
        'masse_horaire_distanciel',
        'type_efm',
        'semestre',
        'filiere_id'
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'affectations')
            ->withPivot('groupe_id');
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
