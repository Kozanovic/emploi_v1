<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 
        'email', 
        'password', 
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function directeur()
    {
        return $this->hasOne(Directeur::class);
    }

    public function formateur()
    {
        return $this->hasOne(Formateur::class);
    }

    public function estFormateur()
    {
        return $this->role === 'Formateur';
    }

    public function estDirecteur()
    {
        return $this->role === 'Directeur';
    }
}
