<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'species_id',
        'location_id',
        'observer_id',
        'observation_date',
        'notes',
    ];

    public function plantSpecies(): BelongsTo
    {
        return $this->belongsTo(PlantSpecies::class, 'species_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function observer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'observer_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'observation_id');
    }
}
