<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statement extends Model
{
    protected $fillable = [
        'registration_id',
        'willing_to_serve',
        'agree_morality',
        'agree_rules',
        'agree_payment',
        'submitted_at',
    ];

    protected $casts = [
        'willing_to_serve' => 'boolean',
        'agree_morality' => 'boolean',
        'agree_rules' => 'boolean',
        'agree_payment' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
