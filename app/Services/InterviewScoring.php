<?php

namespace App\Services;

class InterviewScoring
{
    public static function calculate(array $answers, bool $bonus): array
    {
        $scores = [];
        $reasons = [];
        $complete = true;
        foreach (config('ppdb_interview.sections') as $section => $group) {
            foreach ($group['questions'] as $key => $question) {
                $grade = $answers[$section][$key] ?? null;
                if (!$grade || !array_key_exists($grade, $question['options'])) {
                    $complete = false;
                    continue;
                }
                $scores[] = config('ppdb_interview.weights')[$grade];
                if ($grade === 'C' && ($question['reject_c'] ?? false)) {
                    $reasons[] = $group['label'] . ': ' . $question['label'] . ' — jawaban C';
                }
            }
        }
        $base = $complete && count($scores) ? array_sum($scores) / (count($scores) * max(config('ppdb_interview.weights'))) * 100 : null;
        $total = $base !== null ? $base + ($bonus ? 5 : 0) : null;

        return [
            'base_score' => $base !== null ? round($base, 2) : null,
            'bonus_points' => $bonus ? 5 : 0,
            'total_score' => $total !== null ? round($total, 2) : null,
            'recommendation' => $reasons ? 'tidak_direkomendasikan' : ($total === null ? 'pending' :
                ($total > config('ppdb_interview.threshold') ? 'sangat_direkomendasikan' : 'direkomendasikan')),
            'disqualification_reasons' => $reasons,
        ];
    }
}
