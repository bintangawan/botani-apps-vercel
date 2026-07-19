<?php

namespace App\Services;

use App\Models\Taxa;
use Illuminate\Database\Eloquent\Collection;

class TaxonomyService
{
    /**
     * Get all unique plant families.
     */
    public function getAllFamilies(): Collection
    {
        return Taxa::where('rank', 'famili')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
    }

    /**
     * Get hierarchical tree starting from Kingdom down to species.
     */
    public function getTaxonomyTree(): Collection
    {
        return Taxa::with('children.children.children')
            ->where('rank', 'kingdom')
            ->get();
    }
}
