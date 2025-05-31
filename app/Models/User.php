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
    public function directeurSuper()
    {
        return $this->hasOne(DirecteurSuper::class, 'utilisateur_id');
    }
    public function directeurRegional()
    {
        return $this->hasOne(DirecteurRegional::class, 'utilisateur_id');
    }
    public function directeurComplexe()
    {
        return $this->hasOne(DirecteurComplexe::class, 'utilisateur_id');
    }
    public function directeurEtablissement()
    {
        return $this->hasOne(DirecteurEtablissement::class, 'utilisateur_id');
    }
    public function formateur()
    {
        return $this->hasOne(Formateur::class, 'utilisateur_id');
    }
    // public function stagiaire()
    // {
    //     return $this->hasOne(Stagiaire::class,'utilisateur_id');
    // }

    public function estDirecteurSuper()
    {
        return $this->role === 'DirecteurSuper';
    }
    public function estDirecteurRegional()
    {
        return $this->role === 'DirecteurRegional';
    }
    public function estDirecteurComplexe()
    {
        return $this->role === 'DirecteurComplexe';
    }
    public function estDirecteurEtablissement()
    {
        return $this->role === 'DirecteurEtablissement';
    }
    public function estFormateur()
    {
        return $this->role === 'Formateur';
    }
    // public function estStagiaire()
    // {
    //     return $this->role === 'Stagiaire';
    // }
}
