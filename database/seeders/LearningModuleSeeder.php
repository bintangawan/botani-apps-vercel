<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use RuntimeException;

class LearningModuleSeeder extends Seeder
{
    /**
     * Replace the old learning modules with the nine chapters in data/modules.json.
     */
    public function run(): void
    {
        $indexPath = base_path('data/modules.json');
        $index = $this->readJson($indexPath);
        $chapters = $index['chapters'] ?? null;

        if (! is_array($chapters) || count($chapters) !== 9) {
            throw new RuntimeException('data/modules.json harus berisi tepat 9 bab.');
        }

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        DB::transaction(function () use ($chapters, $converter): void {
            // Seeder ini bersifat sinkronisasi penuh agar tidak menyisakan modul seed lama.
            LearningModule::query()->delete();

            foreach ($chapters as $chapterIndex) {
                $file = $chapterIndex['file'] ?? null;
                if (! is_string($file) || $file === '') {
                    throw new RuntimeException('Setiap bab di data/modules.json wajib memiliki file sumber.');
                }

                $chapter = $this->readJson(base_path('data/'.$file));
                $lessons = $chapter['lessons'] ?? null;

                if (! is_array($lessons) || count($lessons) !== (int) ($chapterIndex['lesson_count'] ?? -1)) {
                    throw new RuntimeException("Jumlah lesson pada {$file} tidak sesuai dengan indeks modul.");
                }

                $module = LearningModule::create([
                    'title' => $chapter['title'],
                    'slug' => $chapter['slug'],
                    'description' => $chapter['description'] ?? '',
                    'module_order' => (int) $chapter['chapter'],
                    'estimated_minutes' => (int) ($chapter['estimated_minutes'] ?? 0),
                    'chapter_summary' => $chapter['chapter_summary'] ?? [],
                    'source_file' => $file,
                    'status' => 'published',
                ]);

                foreach ($lessons as $lesson) {
                    $module->lessons()->create([
                        'source_id' => $lesson['id'],
                        'title' => $lesson['title'],
                        'slug' => $lesson['slug'],
                        'content' => (string) $converter->convert($lesson['content'] ?? ''),
                        'content_format' => 'html',
                        'key_points' => $lesson['key_points'] ?? [],
                        'source_sections' => $lesson['source_sections'] ?? [],
                        'lesson_order' => (int) $lesson['order'],
                    ]);
                }
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function readJson(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("File data modul tidak ditemukan: {$path}");
        }

        $data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw new RuntimeException("Struktur JSON modul tidak valid: {$path}");
        }

        return $data;
    }
}
