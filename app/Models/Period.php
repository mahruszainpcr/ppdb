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
        'registration_open_date',
        'registration_close_date',
        'exam_date',
        'announce_date',
        'down_payment_deadline',
        'wa_group_ikhwan',
        'wa_group_akhwat',
        'admin_contact_1',
        'admin_contact_2',
        'information_note',
        'payment_proof_label',
        'payment_proof_note',
        'payment_agreement_note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registration_open_date' => 'date',
        'registration_close_date' => 'date',
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

    public function isRegistrationOpen(): bool
    {
        $today = now()->startOfDay();

        if ($this->registration_open_date && $today < $this->registration_open_date->startOfDay()) {
            return false;
        }

        if ($this->registration_close_date && $today > $this->registration_close_date->endOfDay()) {
            return false;
        }

        return true;
    }

    public function isRegistrationClosed(): bool
    {
        return !$this->isRegistrationOpen();
    }

    public function registrationClosureMessage(): string
    {
        if ($this->registration_close_date && now()->greaterThan($this->registration_close_date->endOfDay())) {
            return 'Pendaftaran PPDB ditutup sejak ' . $this->registration_close_date->translatedFormat('d M Y') . '. Informasi selanjutnya dapat dilihat di halaman informasi PPDB atau melalui kontak admin.';
        }

        if ($this->registration_open_date && now()->lt($this->registration_open_date->startOfDay())) {
            return 'Pendaftaran PPDB belum dibuka. Pembukaan pendaftaran dijadwalkan pada ' . $this->registration_open_date->translatedFormat('d M Y') . '.';
        }

        return 'Pendaftaran PPDB untuk periode ini sedang dibuka.';
    }
}
