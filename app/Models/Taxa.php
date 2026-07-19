<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxa extends Model
{
    use HasFactory;

    protected $table = 'taxa';

    protected $fillable = [
        'parent_id',
        'name',
        'rank',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxa::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxa::class, 'parent_id');
    }

    public function plantSpecies(): BelongsToMany
    {
        return $this->belongsToMany(PlantSpecies::class, 'species_taxa', 'taxon_id', 'species_id');
    }
}
