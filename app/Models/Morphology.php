<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Morphology extends Model
{
    use HasFactory;

    protected $fillable = [
        'species_id',
        'root',
        'stem',
        'leaf',
        'flower',
        'fruit',
        'seed',
        'special_characteristics',
    ];

    public function plantSpecies(): BelongsTo
    {
        return $this->belongsTo(PlantSpecies::class, 'species_id');
    }
}
