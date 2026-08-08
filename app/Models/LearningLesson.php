<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'source_id',
        'title',
        'slug',
        'content',
        'content_format',
        'key_points',
        'source_sections',
        'lesson_order',
    ];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
            'source_sections' => 'array',
            'lesson_order' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class, 'module_id');
    }
}
