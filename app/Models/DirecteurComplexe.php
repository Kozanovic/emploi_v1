<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirecteurComplexe extends Model
{
    use HasFactory;
    protected $fillable = ['utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
    public function complexe()
    {
        return $this->hasOne(Complexe::class);
    }
}
