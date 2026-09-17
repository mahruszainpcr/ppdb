<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class StudentProfile extends Model
{
    protected $fillable = [
        'registration_id',
        'full_name',
        'nisn',
        'nik',
        'birth_place',
        'birth_date',
        'address',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'school_origin',
        'hobby',
        'ambition',
        'religion',
        'nationality',
        'siblings_count',
        'child_number',
        'orphan_status',
        'blood_type',
        'medical_history',
        'motivation',
        'quran_memorization_level',
        'quran_reading_level',
        'program_choice',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'siblings_count' => 'integer',
        'child_number' => 'integer',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function save(array $options = [])
    {
        if (!$this->isDirty('program_choice')) {
            return parent::save($options);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($options) {
            $registration = $this->registration()->first();
            if ($registration?->period_id) {
                $period = Period::query()->lockForUpdate()->findOrFail($registration->period_id);
                $registration->refresh();
                if ($registration->graduation_status === 'lulus') {
                    \App\Services\AdmissionQuota::validate($period, $registration, $this->program_choice);
                }
            }

            return parent::save($options);
        });
    }

    public static function schoolOriginOptions(): Collection
    {
        return static::query()
            ->whereNotNull('school_origin')
            ->where('school_origin', '!=', '')
            ->pluck('school_origin')
            ->map(fn(string $schoolOrigin) => mb_strtoupper(trim($schoolOrigin)))
            ->unique()
            ->sort()
            ->values();
    }
}
