<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'period_id',
        'registration_no',
        'funding_type',       // mandiri|beasiswa
        'education_level',    // SMP_NEW|SMA_NEW|SMA_OLD
        'gender',             // male|female
        'status',             // draft|submitted|verified|revision_requested
        'graduation_status',  // pending|lulus|tidak_lulus|cadangan
        'admin_note',
    ];

    protected $casts = [
        // jika nanti pakai enum PHP 8.1 bisa di-upgrade
    ];

    /* ===================== RELATIONS ===================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    // data santri 1-1
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    // data orang tua 1-1
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    // pernyataan 1-1
    public function statement(): HasOne
    {
        return $this->hasOne(Statement::class);
    }

    // dokumen 1-N (unik per type)
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /* ===================== HELPERS ===================== */

    public function documentByType(string $type): ?Document
    {
        return $this->documents->firstWhere('type', $type);
    }

    public function requiredDocumentTypes(): array
    {
        // wajib selalu
        $required = ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER'];

        // kondisional
        if ($this->funding_type === 'beasiswa') {
            $required[] = 'SKTM';
        }
        if ($this->education_level === 'SMA_NEW') {
            $required[] = 'GOOD_BEHAVIOR';
        }

        return $required;
    }

    public function missingRequiredDocuments(): array
    {
        $missing = [];
        foreach ($this->requiredDocumentTypes() as $type) {
            $doc = $this->documents->firstWhere('type', $type);
            if (!$doc || !$doc->file_path) {
                $missing[] = $type;
            }
        }
        return $missing;
    }

    public function isStep1Complete(): bool
    {
        return count($this->missingRequiredDocuments()) === 0;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }
}
