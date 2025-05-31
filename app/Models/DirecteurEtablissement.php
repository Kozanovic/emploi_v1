<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirecteurEtablissement extends Model
{
    use HasFactory;

    protected $fillable = ['utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class,'utilisateur_id');
    }
    public function etablissement()
    {
        return $this->hasOne(Etablissement::class,'directeur_etablissement_id');
    }
}
