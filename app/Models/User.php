<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    public function isTechnician(): bool { return $this->role === 'technician'; }
    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isClient(): bool      { return $this->role === 'client'; }

    public function scopeTechnician($q) { return $q->where('role', 'technician'); }
    public function scopeAdmin($q)      { return $q->where('role', 'admin'); }
    public function scopeClient($q)     { return $q->where('role', 'client'); }

    public function technicians() { return $this->hasOne(Technician::class); }
    public function clientProfile() { return $this->hasOne(Client::class, 'client_user_id'); }
}