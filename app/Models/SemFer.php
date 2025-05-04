<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemFer extends Model
{
    use HasFactory;

    protected $fillable = ['ferie_id', 'semaine_id'];

    public function ferie()
    {
        return $this->belongsTo(Ferie::class, 'ferie_id');
    }

    public function semaine()
    {
        return $this->belongsTo(Semaine::class);
    }
}
