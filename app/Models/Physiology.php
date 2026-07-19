<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Physiology extends Model
{
    use HasFactory;

    protected $fillable = [
        'species_id',
        'reproduction',
        'growth',
        'adaptation',
        'additional_information',
    ];

    public function plantSpecies(): BelongsTo
    {
        return $this->belongsTo(PlantSpecies::class, 'species_id');
    }
}
