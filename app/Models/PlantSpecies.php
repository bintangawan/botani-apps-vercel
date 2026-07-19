<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PlantSpecies extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'slug',
        'local_name',
        'scientific_name',
        'author_name',
        'group_type',
        'cotyledon_type',
        'description',
        'habitat',
        'benefits',
        'image_path',
        'status',
    ];

    /**
     * Get smart image URL for any path format (seeder, external URL, or admin upload).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path || str_contains($this->image_path, 'default.jpg')) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        if (Str::startsWith($this->image_path, ['/storage/', 'storage/'])) {
            return asset(ltrim($this->image_path, '/'));
        }

        return asset('storage/' . $this->image_path);
    }

    public function morphology(): HasOne
    {
        return $this->hasOne(Morphology::class, 'species_id');
    }

    public function physiology(): HasOne
    {
        return $this->hasOne(Physiology::class, 'species_id');
    }

    public function taxa(): BelongsToMany
    {
        return $this->belongsToMany(Taxa::class, 'species_taxa', 'species_id', 'taxon_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'species_id');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(PlantObservation::class, 'species_id');
    }

    public function learningModules(): BelongsToMany
    {
        return $this->belongsToMany(LearningModule::class, 'module_species', 'species_id', 'module_id');
    }
}
