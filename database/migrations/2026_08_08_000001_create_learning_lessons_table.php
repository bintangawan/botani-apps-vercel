<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->unsignedInteger('estimated_minutes')->default(0)->after('module_order');
            $table->json('chapter_summary')->nullable()->after('estimated_minutes');
            $table->string('source_file')->nullable()->after('chapter_summary');
        });

        Schema::create('learning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->string('source_id')->nullable()->unique();
            $table->string('title');
            $table->string('slug');
            $table->longText('content');
            $table->string('content_format', 20)->default('html');
            $table->json('key_points')->nullable();
            $table->json('source_sections')->nullable();
            $table->unsignedInteger('lesson_order')->default(1);
            $table->timestamps();

            $table->unique(['module_id', 'slug']);
            $table->unique(['module_id', 'lesson_order']);
        });

        // Preserve installations that already contain the legacy one-content-per-module data.
        $now = now();
        DB::table('learning_modules')->orderBy('id')->each(function (object $module) use ($now): void {
            DB::table('learning_lessons')->insert([
                'module_id' => $module->id,
                'source_id' => 'legacy-module-'.$module->id,
                'title' => $module->title,
                'slug' => $module->slug,
                'content' => $module->content,
                'content_format' => 'html',
                'lesson_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        Schema::table('learning_modules', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('description');
        });

        DB::table('learning_modules')->orderBy('id')->each(function (object $module): void {
            $content = DB::table('learning_lessons')
                ->where('module_id', $module->id)
                ->orderBy('lesson_order')
                ->value('content');

            DB::table('learning_modules')->where('id', $module->id)->update(['content' => $content]);
        });

        Schema::dropIfExists('learning_lessons');

        Schema::table('learning_modules', function (Blueprint $table) {
            $table->dropColumn(['estimated_minutes', 'chapter_summary', 'source_file']);
        });
    }
};
