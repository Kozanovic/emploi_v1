<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialite',
        'peut_gerer_seance',
        'utilisateur_id',
        'etablissement_id',
        'complexe_id',
        'direction_regional_id'
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class,'utilisateur_id');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
    public function complexe()
    {
        return $this->belongsTo(Complexe::class);
    }
    public function direction_regional()
    {
        return $this->belongsTo(DirectionRegional::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'affectations')
            ->withPivot('groupe_id');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
