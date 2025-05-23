<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['nom','secteur_id'];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }
    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function etablissements()
    {
        return $this->belongsToMany(Etablissement::class, 'offrirs');
    }
}
