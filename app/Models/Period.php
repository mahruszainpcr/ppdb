<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $fillable = [
        'name',
        'wave',
        'is_active',
        'exam_date',
        'announce_date',
        'down_payment_deadline',
        'wa_group_ikhwan',
        'wa_group_akhwat',
        'admin_contact_1',
        'admin_contact_2',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'exam_date' => 'date',
        'announce_date' => 'date',
        'down_payment_deadline' => 'date',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
