<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }
        return $this->roles->whereIn('slug', (array) $roles)->isNotEmpty();
    }

    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }
        $roles = (array) $roles;
        return $this->roles->whereIn('slug', $roles)->count() === count($roles);
    }

    public function getRoleNames()
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function getRoleSlugs()
    {
        return $this->roles->pluck('slug')->toArray();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        // Generar avatar con iniciales
        $initials = collect(explode(' ', $this->name))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->implode('');
            
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&color=7F9CF5&background=EBF4FF';
    }
}
