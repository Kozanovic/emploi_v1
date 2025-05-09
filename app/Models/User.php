<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory,Notifiable, HasApiTokens;

    protected $fillable = ['nom', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function directeur()
    {
        return $this->hasOne(Directeur::class);
    }

    public function formateur()
    {
        return $this->hasOne(Formateur::class, 'utilisateur_id');
    }
}
