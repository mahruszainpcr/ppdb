<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // parent|admin|ustadz
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 1 User (parent) bisa punya banyak pendaftaran (kalau nanti ingin multiple anak)
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    // Helper: ambil pendaftaran terbaru
    public function latestRegistration()
    {
        return $this->registrations()->latest('id')->first();
    }

    public function scopeParent($query)
    {
        return $query->where('role', 'parent');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeUstadz($query)
    {
        return $query->where('role', 'ustadz');
    }
}
