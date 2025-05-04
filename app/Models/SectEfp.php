<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectEfp extends Model
{
    use HasFactory;

    protected $fillable = ['secteur_id', 'etablissement_id'];

    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'secteur_id');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}
