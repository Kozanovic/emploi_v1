<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'email', 'password', 'role'];

    public function directeur()
    {
        return $this->hasOne(Directeur::class);
    }

    public function formateur()
    {
        return $this->hasOne(Formateur::class, 'utilisateur_id');
    }
}
