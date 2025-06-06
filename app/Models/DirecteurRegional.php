<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirecteurRegional extends Model
{
    use HasFactory;
    protected $fillable = ['utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
    public function directionRegional()
    {
        return $this->hasOne(DirectionRegional::class, 'directeur_regional_id');
    }
}
