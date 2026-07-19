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
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content'); // Acuan teori
            $table->integer('module_order')->default(1);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });

        Schema::create('module_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('learning_modules')->onDelete('cascade');
            $table->foreignId('species_id')->constrained('plant_species')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('learning_modules')->onDelete('cascade');
            $table->string('title');
            $table->enum('quiz_type', ['pilihan_ganda', 'esai', 'campuran', 'pretest', 'practice', 'posttest'])->default('pilihan_ganda');
            $table->integer('passing_score')->default(60);
            $table->integer('duration')->default(30); // in minutes
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['pilihan_ganda', 'esai', 'multiple_choice', 'true_false'])->default('pilihan_ganda');
            $table->integer('score_weight')->default(1);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->integer('total_correct')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->onDelete('set null');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('module_species');
        Schema::dropIfExists('learning_modules');
    }
};
