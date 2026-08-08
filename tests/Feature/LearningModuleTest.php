<?php

namespace Tests\Feature;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\User;
use Database\Seeders\LearningModuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_seeder_creates_nine_chapters_and_sixty_two_database_lessons(): void
    {
        $this->seed(LearningModuleSeeder::class);

        $this->assertDatabaseCount('learning_modules', 9);
        $this->assertDatabaseCount('learning_lessons', 62);
        $this->assertDatabaseHas('learning_modules', [
            'module_order' => 1,
            'slug' => 'struktur-dan-fungsi-biji',
            'estimated_minutes' => 35,
        ]);
        $this->assertDatabaseHas('learning_lessons', [
            'source_id' => 'bab-1-01',
            'slug' => 'pengantar-struktur-dan-fungsi-biji',
            'lesson_order' => 1,
        ]);

        $tableLesson = LearningLesson::where('source_id', 'bab-1-03')->firstOrFail();
        $this->assertStringContainsString('<table>', $tableLesson->content);

        $response = $this->get(route('modules.detail', [
            'slug' => 'struktur-dan-fungsi-biji',
            'lessonSlug' => 'pengantar-struktur-dan-fungsi-biji',
        ]));

        $response->assertOk()
            ->assertSee('Pengantar Struktur dan Fungsi Biji')
            ->assertSee('Evolusi Tumbuhan dan SDGs')
            ->assertSee('Lesson 1 dari 62');
    }

    public function test_dosen_can_create_a_chapter_with_multiple_quill_lessons(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'status' => 'active']);

        $response = $this->actingAs($dosen)->post(route('manage.modules.store'), [
            'title' => 'Bab Pengujian',
            'description' => 'Deskripsi bab pengujian.',
            'module_order' => 10,
            'estimated_minutes' => 25,
            'chapter_summary' => "Ringkasan pertama\nRingkasan kedua",
            'status' => 'published',
            'lessons' => [
                [
                    'title' => 'Subbab Pertama',
                    'slug' => '',
                    'content' => '<h2>Materi pertama</h2><p>Isi Quill pertama.</p>',
                    'key_points' => "Poin A\nPoin B",
                    'source_sections' => '10.1, 10.2',
                    'lesson_order' => 1,
                ],
                [
                    'title' => 'Subbab Kedua',
                    'slug' => 'subbab-kedua-khusus',
                    'content' => '<p>Isi Quill kedua.</p>',
                    'key_points' => '',
                    'source_sections' => '',
                    'lesson_order' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('manage.modules.index'));
        $module = LearningModule::where('slug', 'bab-pengujian')->firstOrFail();

        $this->assertCount(2, $module->lessons);
        $this->assertSame(['Ringkasan pertama', 'Ringkasan kedua'], $module->chapter_summary);
        $this->assertSame(['Poin A', 'Poin B'], $module->lessons->first()->key_points);
        $this->assertSame(['10.1', '10.2'], $module->lessons->first()->source_sections);
        $this->assertDatabaseHas('learning_lessons', [
            'module_id' => $module->id,
            'slug' => 'subbab-kedua-khusus',
        ]);

        $this->actingAs($dosen)
            ->get(route('manage.modules.edit', $module))
            ->assertOk()
            ->assertSee('Subbab Pertama')
            ->assertSee('data-add-lesson', false)
            ->assertSee('quill@2.0.3', false);
    }

    public function test_editing_a_chapter_preserves_existing_lesson_ids_and_synchronizes_the_list(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'status' => 'active']);
        $module = LearningModule::create([
            'title' => 'Bab Lama',
            'slug' => 'bab-lama',
            'description' => 'Deskripsi lama',
            'module_order' => 1,
            'estimated_minutes' => 20,
            'status' => 'published',
        ]);
        $keptLesson = $module->lessons()->create([
            'title' => 'Lesson Dipertahankan',
            'slug' => 'lesson-dipertahankan',
            'content' => '<p>Isi lama</p>',
            'lesson_order' => 1,
        ]);
        $removedLesson = $module->lessons()->create([
            'title' => 'Lesson Dihapus',
            'slug' => 'lesson-dihapus',
            'content' => '<p>Akan dihapus</p>',
            'lesson_order' => 2,
        ]);

        $response = $this->actingAs($dosen)->put(route('manage.modules.update', $module), [
            'title' => 'Bab Baru',
            'description' => 'Deskripsi baru',
            'module_order' => 1,
            'estimated_minutes' => 30,
            'chapter_summary' => '',
            'status' => 'published',
            'lessons' => [
                [
                    'id' => $keptLesson->id,
                    'title' => 'Lesson Diperbarui',
                    'slug' => 'lesson-diperbarui',
                    'content' => '<p>Isi baru</p>',
                    'key_points' => '',
                    'source_sections' => '',
                    'lesson_order' => 1,
                ],
                [
                    'title' => 'Lesson Tambahan',
                    'slug' => '',
                    'content' => '<p>Isi tambahan</p>',
                    'key_points' => '',
                    'source_sections' => '',
                    'lesson_order' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('manage.modules.index'));
        $this->assertDatabaseHas('learning_lessons', [
            'id' => $keptLesson->id,
            'title' => 'Lesson Diperbarui',
            'lesson_order' => 1,
        ]);
        $this->assertDatabaseMissing('learning_lessons', ['id' => $removedLesson->id]);
        $this->assertDatabaseHas('learning_lessons', [
            'module_id' => $module->id,
            'slug' => 'lesson-tambahan',
            'lesson_order' => 2,
        ]);
    }
}
