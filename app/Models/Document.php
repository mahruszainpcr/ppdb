<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'registration_id',
        'type',
        'file_path',
        'is_required',
        'is_verified',
        'note',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function getPublicUrlAttribute(): ?string
    {
        if (!$this->file_path)
            return null;
        return asset('storage/' . $this->file_path);
    }
}
