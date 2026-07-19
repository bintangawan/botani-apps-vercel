<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plant_species', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // flora_001, etc.
            $table->string('slug')->unique();
            $table->string('local_name');
            $table->string('scientific_name');
            $table->string('author_name')->nullable();
            $table->enum('group_type', ['Gymnospermae', 'Angiospermae']);
            $table->enum('cotyledon_type', ['Monokotil', 'Dikotil', 'Tidak Berlaku']);
            $table->text('description'); // Teori / deskripsi umum
            $table->text('habitat')->nullable();
            $table->text('benefits')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });

        Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('taxa')->onDelete('cascade');
            $table->string('name');
            $table->enum('rank', ['kingdom', 'divisi', 'kelas', 'ordo', 'famili', 'genus', 'spesies']);
            $table->timestamps();
        });

        Schema::create('species_taxa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('plant_species')->onDelete('cascade');
            $table->foreignId('taxon_id')->constrained('taxa')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('morphologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->unique()->constrained('plant_species')->onDelete('cascade');
            $table->text('root')->nullable();
            $table->text('stem')->nullable();
            $table->text('leaf')->nullable();
            $table->text('flower')->nullable();
            $table->text('fruit')->nullable();
            $table->text('seed')->nullable();
            $table->text('special_characteristics')->nullable();
            $table->timestamps();
        });

        Schema::create('physiologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->unique()->constrained('plant_species')->onDelete('cascade');
            $table->text('reproduction')->nullable();
            $table->text('growth')->nullable();
            $table->text('adaptation')->nullable();
            $table->text('additional_information')->nullable();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_name');
            $table->string('village')->nullable();
            $table->string('district');
            $table->string('regency');
            $table->string('province')->default('Sumatera Utara');
            $table->timestamps();
        });

        Schema::create('plant_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('plant_species')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('observer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('observation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->nullable()->constrained('plant_species')->onDelete('cascade');
            $table->foreignId('observation_id')->nullable()->constrained('plant_observations')->onDelete('cascade');
            $table->string('filename');
            $table->string('file_path');
            $table->enum('media_type', ['image', 'diagram', 'video'])->default('image');
            $table->text('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('plant_observations');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('physiologies');
        Schema::dropIfExists('morphologies');
        Schema::dropIfExists('species_taxa');
        Schema::dropIfExists('taxa');
        Schema::dropIfExists('plant_species');
    }
};
