<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class AdminDocument extends Model
{
    public const CATEGORY_OPTIONS = [
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'category',
        'custom_category',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category === 'lainnya') {
            return $this->custom_category ?: 'Lainnya';
        }

        return self::CATEGORY_OPTIONS[$this->category] ?? $this->category;
    }

    public static function customCategoryOptions(): Collection
    {
        return static::query()
            ->where('category', 'lainnya')
            ->whereNotNull('custom_category')
            ->where('custom_category', '!=', '')
            ->orderBy('custom_category')
            ->pluck('custom_category')
            ->unique()
            ->values();
    }
}
