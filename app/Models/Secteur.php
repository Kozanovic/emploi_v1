<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function etablissements()
    {
        return $this->belongsToMany(Etablissement::class, 'sect_efps');
    }
    public function filieres()
    {
        return $this->hasMany(Filiere::class);
    }
}
