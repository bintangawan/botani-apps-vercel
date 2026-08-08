<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'module_order',
        'estimated_minutes',
        'chapter_summary',
        'source_file',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'chapter_summary' => 'array',
            'module_order' => 'integer',
            'estimated_minutes' => 'integer',
        ];
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LearningLesson::class, 'module_id')->orderBy('lesson_order');
    }

    public function plantSpecies(): BelongsToMany
    {
        return $this->belongsToMany(PlantSpecies::class, 'module_species', 'module_id', 'species_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'module_id');
    }
}
