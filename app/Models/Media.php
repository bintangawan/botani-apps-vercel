<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'species_id',
        'observation_id',
        'filename',
        'file_path',
        'media_type',
        'caption',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function plantSpecies(): BelongsTo
    {
        return $this->belongsTo(PlantSpecies::class, 'species_id');
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(PlantObservation::class, 'observation_id');
    }
}
