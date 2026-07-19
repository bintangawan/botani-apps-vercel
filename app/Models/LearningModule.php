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
        'content',
        'module_order',
        'status',
    ];

    public function plantSpecies(): BelongsToMany
    {
        return $this->belongsToMany(PlantSpecies::class, 'module_species', 'module_id', 'species_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'module_id');
    }
}
