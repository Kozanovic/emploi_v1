<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complexe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'direction_regional_id', 'directeur_complexe_id'];

    public function directionRegional()
    {
        return $this->belongsTo(DirectionRegional::class);
    }
    public function directeurComplexe()
    {
        return $this->belongsTo(DirecteurComplexe::class);
    }
    public function etablissements()
    {
        return $this->hasMany(Etablissement::class);
    }
    public function formateurs()
    {
        return $this->hasMany(Formateur::class);
    }
}
