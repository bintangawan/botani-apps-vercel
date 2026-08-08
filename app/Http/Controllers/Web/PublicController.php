<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\LearningModuleService;
use App\Services\ObservationService;
use App\Services\PlantSpeciesService;
use App\Services\TaxonomyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    protected PlantSpeciesService $plantService;

    protected TaxonomyService $taxonomyService;

    protected LearningModuleService $moduleService;

    protected ObservationService $observationService;

    public function __construct(
        PlantSpeciesService $plantService,
        TaxonomyService $taxonomyService,
        LearningModuleService $moduleService,
        ObservationService $observationService
    ) {
        $this->plantService = $plantService;
        $this->taxonomyService = $taxonomyService;
        $this->moduleService = $moduleService;
        $this->observationService = $observationService;
    }

    /**
     * Display landing page / home.
     */
    public function home(): View
    {
        $featuredPlants = $this->plantService->getFeaturedPlants(6);
        $stats = $this->plantService->getStatistics();
        $modules = $this->moduleService->getAllPublishedModules()->take(3);
        $recentObservations = $this->observationService->getRecentObservations(4);

        return view('pages.public.home', compact('featuredPlants', 'stats', 'modules', 'recentObservations'));
    }

    /**
     * Display botanical catalog / gallery.
     */
    public function catalog(Request $request): View
    {
        $filters = $request->only(['search', 'group_type', 'cotyledon_type', 'family', 'regency']);
        $plants = $this->plantService->getCatalogPlants($filters, 12);
        $families = $this->taxonomyService->getAllFamilies();
        $regencies = $this->observationService->getAllRegencies();

        return view('pages.public.catalog', compact('plants', 'families', 'regencies', 'filters'));
    }

    /**
     * Display plant details by slug.
     */
    public function plantDetail(string $slug): View
    {
        $plant = $this->plantService->getPlantBySlug($slug);

        if (! $plant) {
            abort(404, 'Spesimen tumbuhan tidak ditemukan dalam katalog.');
        }

        return view('pages.public.detail', compact('plant'));
    }

    /**
     * Display learning modules list.
     */
    public function modules(): View
    {
        $modules = $this->moduleService->getAllPublishedModules();

        return view('pages.public.modules', compact('modules'));
    }

    /**
     * Display single learning module detail.
     */
    public function moduleDetail(string $slug, ?string $lessonSlug = null): View
    {
        $module = $this->moduleService->getModuleBySlug($slug);

        if (! $module) {
            abort(404, 'Modul pembelajaran tidak ditemukan.');
        }

        $lesson = $lessonSlug
            ? $module->lessons->firstWhere('slug', $lessonSlug)
            : $module->lessons->first();

        if (! $lesson) {
            abort(404, 'Subbab pembelajaran tidak ditemukan.');
        }

        $courseModules = $this->moduleService->getCourseOutline();
        $navigation = $courseModules->flatMap(fn ($chapter) => $chapter->lessons->map(fn ($chapterLesson) => [
            'module_slug' => $chapter->slug,
            'module_title' => $chapter->title,
            'lesson_slug' => $chapterLesson->slug,
            'lesson_title' => $chapterLesson->title,
            'lesson_id' => $chapterLesson->id,
        ]))->values();
        $currentPosition = $navigation->search(fn (array $item) => $item['lesson_id'] === $lesson->id);
        $previousLesson = $currentPosition !== false && $currentPosition > 0
            ? $navigation->get($currentPosition - 1)
            : null;
        $nextLesson = $currentPosition !== false && $currentPosition < $navigation->count() - 1
            ? $navigation->get($currentPosition + 1)
            : null;
        $lessonNumber = $currentPosition === false ? 1 : $currentPosition + 1;
        $totalLessons = $navigation->count();

        return view('pages.public.module-detail', compact(
            'module',
            'lesson',
            'courseModules',
            'previousLesson',
            'nextLesson',
            'lessonNumber',
            'totalLessons',
        ));
    }

    /**
     * Display about page.
     */
    public function about(): View
    {
        return view('pages.public.about');
    }
}
