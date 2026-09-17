<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbOralExam extends Model
{
    protected $fillable = ['question_1_grade', 'question_2_grade', 'question_3_grade', 'notes', 'examiner_id'];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}
