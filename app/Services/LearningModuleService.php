<?php

namespace App\Services;

use App\Models\LearningModule;
use Illuminate\Database\Eloquent\Collection;

class LearningModuleService
{
    /**
     * Get all published learning modules ordered chronologically.
     */
    public function getAllPublishedModules(): Collection
    {
        return LearningModule::with('plantSpecies')
            ->where('status', 'published')
            ->orderBy('module_order', 'asc')
            ->get();
    }

    /**
     * Get specific learning module by slug.
     */
    public function getModuleBySlug(string $slug): ?LearningModule
    {
        return LearningModule::with(['plantSpecies.media', 'quizzes'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }
}
