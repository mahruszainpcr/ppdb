<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
