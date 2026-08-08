<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\PlantSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ModuleManagementController extends Controller
{
    /**
     * Display a listing of learning chapters.
     */
    public function index(Request $request): View
    {
        $query = LearningModule::withCount(['lessons', 'quizzes', 'plantSpecies']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('lessons', fn ($lessonQuery) => $lessonQuery->where('title', 'like', "%{$search}%"));
            });
        }

        $modules = $query->orderBy('module_order')->paginate(10)->withQueryString();

        return view('pages.manage.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a chapter with one or more lessons.
     */
    public function create(): View
    {
        $plants = PlantSpecies::where('status', 'published')->orderBy('local_name')->get();
        $nextOrder = ((int) LearningModule::max('module_order')) + 1;

        return view('pages.manage.modules.create', compact('plants', 'nextOrder'));
    }

    /**
     * Store a newly created chapter and all submitted lessons.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateModule($request);
        $lessonPayloads = $this->prepareLessons($validated['lessons']);

        $module = DB::transaction(function () use ($validated, $lessonPayloads): LearningModule {
            $module = LearningModule::create($this->modulePayload($validated));
            $this->syncLessons($module, $lessonPayloads);
            $module->plantSpecies()->sync($validated['species'] ?? []);

            return $module;
        });

        return redirect()->route('manage.modules.index')
            ->with('success', "Bab '{$module->title}' beserta ".count($lessonPayloads).' subbab berhasil dibuat!');
    }

    /**
     * Show the form for editing a chapter and its lessons.
     */
    public function edit(LearningModule $module): View
    {
        $module->load(['plantSpecies', 'lessons']);
        $plants = PlantSpecies::where('status', 'published')->orderBy('local_name')->get();
        $selectedSpecies = $module->plantSpecies->pluck('id')->all();

        return view('pages.manage.modules.edit', compact('module', 'plants', 'selectedSpecies'));
    }

    /**
     * Update the chapter and synchronize its lessons.
     */
    public function update(Request $request, LearningModule $module): RedirectResponse
    {
        $validated = $this->validateModule($request, $module);
        $lessonPayloads = $this->prepareLessons($validated['lessons']);

        DB::transaction(function () use ($module, $validated, $lessonPayloads): void {
            $module->update($this->modulePayload($validated, $module));
            $this->syncLessons($module, $lessonPayloads);
            $module->plantSpecies()->sync($validated['species'] ?? []);
        });

        return redirect()->route('manage.modules.index')
            ->with('success', "Bab '{$module->title}' beserta seluruh subbab berhasil diperbarui!");
    }

    /**
     * Remove the chapter and all dependent lessons.
     */
    public function destroy(LearningModule $module): RedirectResponse
    {
        $title = $module->title;
        $module->delete();

        return redirect()->route('manage.modules.index')
            ->with('success', "Bab '{$title}' dan seluruh subbabnya telah dihapus dari sistem.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validateModule(Request $request, ?LearningModule $module = null): array
    {
        $lessonIdRule = Rule::exists('learning_lessons', 'id');
        if ($module) {
            $lessonIdRule->where(fn ($query) => $query->where('module_id', $module->id));
        }

        $lessonIdRules = $module
            ? ['nullable', 'integer', $lessonIdRule]
            : ['prohibited'];

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'module_order' => ['required', 'integer', 'min:1'],
            'estimated_minutes' => ['required', 'integer', 'min:0', 'max:10000'],
            'chapter_summary' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'species' => ['nullable', 'array'],
            'species.*' => ['integer', 'exists:plant_species,id'],
            'lessons' => ['required', 'array', 'min:1'],
            'lessons.*.id' => $lessonIdRules,
            'lessons.*.title' => ['required', 'string', 'max:255'],
            'lessons.*.slug' => ['nullable', 'string', 'max:255'],
            'lessons.*.content' => ['required', 'string'],
            'lessons.*.key_points' => ['nullable', 'string'],
            'lessons.*.source_sections' => ['nullable', 'string'],
            'lessons.*.lesson_order' => ['required', 'integer', 'min:1', 'distinct'],
        ], [
            'lessons.required' => 'Minimal satu subbab harus ditambahkan.',
            'lessons.min' => 'Minimal satu subbab harus ditambahkan.',
            'lessons.*.title.required' => 'Judul setiap subbab wajib diisi.',
            'lessons.*.content.required' => 'Isi materi setiap subbab wajib diisi.',
        ]);
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $lessons
     * @return array<int, array<string, mixed>>
     */
    private function prepareLessons(array $lessons): array
    {
        $prepared = [];
        $usedSlugs = [];

        usort($lessons, fn (array $left, array $right) => (int) $left['lesson_order'] <=> (int) $right['lesson_order']);

        foreach (array_values($lessons) as $index => $lesson) {
            $slug = Str::slug($lesson['slug'] ?: $lesson['title']);

            if ($slug === '') {
                throw ValidationException::withMessages([
                    "lessons.{$index}.slug" => 'Slug subbab tidak dapat dibuat dari judul tersebut.',
                ]);
            }

            if (isset($usedSlugs[$slug])) {
                throw ValidationException::withMessages([
                    "lessons.{$index}.slug" => "Slug subbab '{$slug}' digunakan lebih dari sekali.",
                ]);
            }

            $usedSlugs[$slug] = true;
            $prepared[] = [
                'id' => Arr::get($lesson, 'id'),
                'title' => trim($lesson['title']),
                'slug' => $slug,
                'content' => $lesson['content'],
                'content_format' => 'html',
                'key_points' => $this->splitList($lesson['key_points'] ?? ''),
                'source_sections' => $this->splitList($lesson['source_sections'] ?? '', true),
                'lesson_order' => $index + 1,
            ];
        }

        return $prepared;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function modulePayload(array $validated, ?LearningModule $module = null): array
    {
        return [
            'title' => trim($validated['title']),
            'slug' => $this->uniqueModuleSlug($validated['title'], $module?->id),
            'description' => trim($validated['description'] ?? ''),
            'module_order' => (int) $validated['module_order'],
            'estimated_minutes' => (int) $validated['estimated_minutes'],
            'chapter_summary' => $this->splitList($validated['chapter_summary'] ?? ''),
            'source_file' => $module?->source_file,
            'status' => $validated['status'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lessonPayloads
     */
    private function syncLessons(LearningModule $module, array $lessonPayloads): void
    {
        $existingLessons = $module->lessons()->get()->keyBy('id');
        $keptIds = collect($lessonPayloads)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        $module->lessons()->whereNotIn('id', $keptIds)->delete();

        // Temporary values prevent unique-key collisions when two existing lessons swap order or slug.
        $existingLessons->only($keptIds)->each(function ($lesson): void {
            $lesson->update([
                'slug' => '__updating-'.$lesson->id.'-'.Str::lower(Str::random(8)),
                'lesson_order' => 100000 + $lesson->id,
            ]);
        });

        foreach ($lessonPayloads as $payload) {
            $lessonId = Arr::pull($payload, 'id');

            if ($lessonId && $existingLessons->has((int) $lessonId)) {
                $existingLessons->get((int) $lessonId)->update($payload);
            } else {
                $module->lessons()->create($payload);
            }
        }
    }

    private function uniqueModuleSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'bab-pembelajaran';
        $slug = $baseSlug;
        $suffix = 2;

        while (LearningModule::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * @return array<int, string>
     */
    private function splitList(string $value, bool $allowComma = false): array
    {
        $pattern = $allowComma ? '/[\r\n,]+/' : '/\r\n|\r|\n/';

        return collect(preg_split($pattern, $value) ?: [])
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
