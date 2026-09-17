<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbInterview extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'answers' => 'array', 'distant_city_bonus' => 'boolean',
        'base_score' => 'decimal:2', 'total_score' => 'decimal:2',
        'bonus_points' => 'integer', 'disqualification_reasons' => 'array',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }

    public function getHasRelativeAttribute(): bool
    {
        return in_array($this->answers['child']['relative'] ?? null, ['A', 'B'], true);
    }

    public function getRecommendationLabelAttribute(): string
    {
        return match ($this->recommendation) {
            'sangat_direkomendasikan' => 'Sangat Direkomendasikan',
            'direkomendasikan' => 'Direkomendasikan',
            'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
            default => 'Belum lengkap',
        };
    }
}
