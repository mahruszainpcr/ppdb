<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SantriContinuation extends Model
{
    protected $fillable = [
        'registration_id',
        'full_name',
        'last_class',
        'dormitory',
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'continue_to_ulya',
        'agree_rules',
        'agree_programs',
        'agree_administration',
        'bedding_option',
        'payment_proof_path',
        'signature_path',
        'submitted_at',
    ];

    protected $casts = [
        'continue_to_ulya' => 'boolean',
        'agree_rules' => 'boolean',
        'agree_programs' => 'boolean',
        'agree_administration' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
