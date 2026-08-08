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
        return LearningModule::with([
            'plantSpecies',
            'lessons' => fn ($query) => $query->orderBy('lesson_order'),
        ])
            ->withCount(['lessons', 'quizzes', 'plantSpecies'])
            ->where('status', 'published')
            ->orderBy('module_order', 'asc')
            ->get();
    }

    /**
     * Get specific learning module by slug.
     */
    public function getModuleBySlug(string $slug): ?LearningModule
    {
        return LearningModule::with([
            'plantSpecies.media',
            'quizzes',
            'lessons' => fn ($query) => $query->orderBy('lesson_order'),
        ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }

    /**
     * Get the complete published chapter and lesson outline for course navigation.
     */
    public function getCourseOutline(): Collection
    {
        return LearningModule::query()
            ->select(['id', 'title', 'slug', 'module_order', 'estimated_minutes'])
            ->with(['lessons' => fn ($query) => $query
                ->select(['id', 'module_id', 'title', 'slug', 'lesson_order'])
                ->orderBy('lesson_order')])
            ->where('status', 'published')
            ->orderBy('module_order')
            ->get();
    }
}
