<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ferie extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'date_debut', 'date_fin'];

    public function semaines()
    {
        return $this->belongsToMany(Semaine::class, 'sem_fers');
    }
}
