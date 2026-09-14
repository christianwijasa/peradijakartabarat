<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function candidateAdvocate(): HasOne
    {
        return $this->hasOne(CandidateAdvocate::class);
    }

    public function supervisingLawyer(): HasOne
    {
        return $this->hasOne(SupervisingLawyer::class);
    }

    public function isCandidateAdvocate(): bool
    {
        return $this->role === 'calon_advokat';
    }

    public function isLawFirm(): bool
    {
        return $this->role === 'law_firm';
    }

    public function isAdminDpc(): bool
    {
        return $this->role === 'admin_dpc';
    }
}
