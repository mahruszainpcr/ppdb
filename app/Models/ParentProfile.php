<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentProfile extends Model
{
    protected $fillable = [
        'registration_id',
        'kk_number',

        'father_name',
        'father_nik',
        'father_birth_place',
        'father_birth_date',
        'father_religion',
        'father_education',
        'father_job',
        'father_income',
        'father_address',
        'father_city',
        'father_district',
        'father_postal_code',
        'father_phone',

        'mother_name',
        'mother_nik',
        'mother_birth_place',
        'mother_birth_date',
        'mother_religion',
        'mother_education',
        'mother_job',
        'mother_income',
        'mother_phone',

        'favorite_ustadz',
    ];

    protected $casts = [
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
