<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offrir extends Model
{
    use HasFactory;

    protected $fillable = ['filiere_id', 'etablissement_id'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
