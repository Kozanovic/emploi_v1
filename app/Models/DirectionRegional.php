<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectionRegional extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'adresse', 'telephone', 'directeur_regional_id'];

    public function complexes()
    {
        return $this->hasMany(Complexe::class);
    }
    public function formateurs()
    {
        return $this->hasMany(Formateur::class);
    }
    public function directeurRegional()
    {
        return $this->belongsTo(DirecteurRegional::class);
    }
}
