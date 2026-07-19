<?php

namespace App\Services;

use App\Models\Location;
use App\Models\PlantObservation;
use Illuminate\Database\Eloquent\Collection;

class ObservationService
{
    /**
     * Get all unique observation locations.
     */
    public function getAllLocations(): Collection
    {
        return Location::withCount('observations')
            ->orderBy('location_name', 'asc')
            ->get();
    }

    /**
     * Get all unique regencies (kabupaten) where observations exist.
     */
    public function getAllRegencies(): \Illuminate\Support\Collection
    {
        return Location::select('regency')
            ->whereNotNull('regency')
            ->where('regency', '!=', '')
            ->distinct()
            ->orderBy('regency', 'asc')
            ->pluck('regency');
    }

    /**
     * Get recent field observations with images and species details.
     */
    public function getRecentObservations(int $limit = 12): Collection
    {
        return PlantObservation::with(['plantSpecies', 'location', 'observer', 'media'])
            ->orderBy('observation_date', 'desc')
            ->limit($limit)
            ->get();
    }
}
