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
        'oral_exam_notes',
        'tahfidz_score',
        'tajwid_score',
        'arabic_score',
        'tpa_score',
        'interview_recommendation',
    ];

    protected $casts = [
        'tahfidz_score' => 'decimal:2',
        'tajwid_score' => 'decimal:2',
        'arabic_score' => 'decimal:2',
        'tpa_score' => 'decimal:2',
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

    public function santriContinuation(): HasOne
    {
        return $this->hasOne(SantriContinuation::class);
    }

    // dokumen 1-N (unik per type)
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function eventAttendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    /* ===================== HELPERS ===================== */

    public function documentByType(string $type): ?Document
    {
        return $this->documents->firstWhere('type', $type);
    }

    public function requiredDocumentTypes(): array
    {
        if ($this->education_level === 'SMA_OLD') {
            return [];
        }

        return ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER'];
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
        if ($this->education_level === 'SMA_OLD') {
            return true;
        }

        return count($this->missingRequiredDocuments()) === 0;
    }

    public function isSantriContinuationComplete(): bool
    {
        return $this->education_level === 'SMA_OLD' && (bool) $this->santriContinuation;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function getAdminScanUrlAttribute(): string
    {
        return route('admin.registrations.scan', ['registration' => $this->registration_no]);
    }

    public function getParentQrUrlAttribute(): string
    {
        return route('psb.qr', $this);
    }

    public function getFundingTypeLabelAttribute(): string
    {
        return match ($this->funding_type) {
            'beasiswa' => 'Beasiswa',
            'mandiri' => 'Mandiri',
            default => '-',
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            default => '-',
        };
    }

    public function getEducationLevelLabelAttribute(): string
    {
        return match ($this->education_level) {
            'SMP_NEW' => 'SMP',
            'SMA_NEW', 'SMA_OLD' => 'SMA',
            default => '-',
        };
    }
}
