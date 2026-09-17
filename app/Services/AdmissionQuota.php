<?php

namespace App\Services;

use App\Models\Period;
use App\Models\Registration;
use Illuminate\Validation\ValidationException;

class AdmissionQuota
{
    public static function usage(Period $period, ?int $exclude = null): array
    {
        $accepted = Registration::query()->where('period_id', $period->id)->where('graduation_status', 'lulus');
        if ($exclude) {
            $accepted->where('id', '!=', $exclude);
        }
        $scholarships = (clone $accepted)->where(function ($q) {
            $q->where('admission_decision', 'scholarship')->orWhere(fn ($legacy) => $legacy->whereNull('admission_decision')->where('funding_type', 'beasiswa'));
        });
        $takhosus = (clone $accepted)->where(function ($q) {
            $q->where('admission_decision', 'takhosus')->orWhere(fn ($legacy) => $legacy->whereNull('admission_decision')->whereHas('studentProfile', fn ($profile) => $profile->where('program_choice', 'takhosus')));
        });

        return [
            'scholarship_quota' => $scholarships->count(),
            'takhosus_ikhwan_quota' => (clone $takhosus)->where('gender', 'male')->count(),
            'takhosus_akhwat_quota' => (clone $takhosus)->where('gender', 'female')->count(),
        ];
    }

    public static function validate(Period $period, ?Registration $candidate = null, ?string $program = null): void
    {
        $counts = self::usage($period, $candidate?->id);
        if ($candidate?->graduation_status === 'lulus') {
            if ($candidate->admission_decision === 'scholarship' || ($candidate->admission_decision === null && $candidate->funding_type === 'beasiswa')) {
                $counts['scholarship_quota']++;
            }
            if ($candidate->admission_decision === 'takhosus' || ($candidate->admission_decision === null && $program === 'takhosus')) {
                if (!in_array($candidate->gender, ['male', 'female'], true)) {
                    throw ValidationException::withMessages(['graduation_status' => 'Lengkapi gender santri takhosus sebelum menetapkan kelulusan.']);
                }
                $counts[$candidate->gender === 'male' ? 'takhosus_ikhwan_quota' : 'takhosus_akhwat_quota']++;
            }
        }

        $labels = ['scholarship_quota' => 'beasiswa (gabungan ikhwan dan akhwat)', 'takhosus_ikhwan_quota' => 'takhosus ikhwan', 'takhosus_akhwat_quota' => 'takhosus akhwat'];
        foreach ($counts as $field => $count) {
            if ($count > $period->{$field}) {
                throw ValidationException::withMessages([
                    $candidate ? 'graduation_status' : $field => "Kuota {$labels[$field]} maksimal {$period->{$field}} santri lulus per periode; perubahan ini menghasilkan {$count} santri. Sesuaikan kuota atau status kelulusan terlebih dahulu.",
                ]);
            }
        }
    }
}
