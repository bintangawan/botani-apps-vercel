<?php

namespace App\Services;

use App\Models\PlantSpecies;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PlantSpeciesService
{
    /**
     * Get paginated and filtered plant species for public catalog / gallery.
     */
    public function getCatalogPlants(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = PlantSpecies::with(['morphology', 'taxa', 'media', 'observations.location'])
            ->where('status', 'published');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('local_name', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['group_type']) && $filters['group_type'] !== 'all') {
            $query->where('group_type', $filters['group_type']);
        }

        if (!empty($filters['cotyledon_type']) && $filters['cotyledon_type'] !== 'all') {
            $query->where('cotyledon_type', $filters['cotyledon_type']);
        }

        if (!empty($filters['family']) && $filters['family'] !== 'all') {
            $family = $filters['family'];
            $query->whereHas('taxa', function (Builder $q) use ($family) {
                $q->where('rank', 'famili')->where('name', 'like', "%{$family}%");
            });
        }

        if (!empty($filters['regency']) && $filters['regency'] !== 'all') {
            $regency = $filters['regency'];
            $query->whereHas('observations.location', function (Builder $q) use ($regency) {
                $q->where('regency', $regency);
            });
        }

        return $query->orderBy('local_name', 'asc')->paginate($perPage);
    }

    /**
     * Get featured plants for landing page.
     */
    public function getFeaturedPlants(int $limit = 6): Collection
    {
        return PlantSpecies::with(['taxa', 'media', 'morphology'])
            ->where('status', 'published')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get plant details by slug along with full taxonomy, morphology, physiology, and media.
     */
    public function getPlantBySlug(string $slug): ?PlantSpecies
    {
        return PlantSpecies::with([
            'morphology',
            'physiology',
            'taxa' => function ($q) {
                $q->orderBy('id', 'asc');
            },
            'media',
            'observations.location',
            'observations.observer',
            'learningModules'
        ])->where('slug', $slug)->first();
    }

    /**
     * Get statistics for landing page.
     */
    public function getStatistics(): array
    {
        return [
            'total_species' => PlantSpecies::where('status', 'published')->count(),
            'total_gymnospermae' => PlantSpecies::where('status', 'published')->where('group_type', 'Gymnospermae')->count(),
            'total_angiospermae' => PlantSpecies::where('status', 'published')->where('group_type', 'Angiospermae')->count(),
        ];
    }
}
