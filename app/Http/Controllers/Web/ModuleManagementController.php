<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\PlantSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModuleManagementController extends Controller
{
    /**
     * Display a listing of learning modules.
     */
    public function index(Request $request): View
    {
        $query = LearningModule::withCount(['quizzes', 'plantSpecies']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $modules = $query->orderBy('module_order', 'asc')->paginate(10)->withQueryString();

        return view('pages.manage.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new learning module.
     */
    public function create(): View
    {
        $plants = PlantSpecies::where('status', 'published')->orderBy('local_name', 'asc')->get();
        return view('pages.manage.modules.create', compact('plants'));
    }

    /**
     * Store a newly created learning module.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'module_order' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:published,draft'],
            'species' => ['nullable', 'array'],
            'species.*' => ['exists:plant_species,id'],
        ]);

        $slug = Str::slug($validated['title']);

        $module = LearningModule::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'module_order' => $validated['module_order'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['species'])) {
            $module->plantSpecies()->sync($validated['species']);
        }

        return redirect()->route('manage.modules.index')->with('success', "Modul Pembelajaran '{$module->title}' berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified learning module.
     */
    public function edit(LearningModule $module): View
    {
        $module->load('plantSpecies');
        $plants = PlantSpecies::where('status', 'published')->orderBy('local_name', 'asc')->get();
        $selectedSpecies = $module->plantSpecies->pluck('id')->toArray();

        return view('pages.manage.modules.edit', compact('module', 'plants', 'selectedSpecies'));
    }

    /**
     * Update the specified learning module.
     */
    public function update(Request $request, LearningModule $module): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'module_order' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:published,draft'],
            'species' => ['nullable', 'array'],
            'species.*' => ['exists:plant_species,id'],
        ]);

        $slug = Str::slug($validated['title']);

        $module->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'module_order' => $validated['module_order'],
            'status' => $validated['status'],
        ]);

        $module->plantSpecies()->sync($validated['species'] ?? []);

        return redirect()->route('manage.modules.index')->with('success', "Modul Pembelajaran '{$module->title}' berhasil diperbarui!");
    }

    /**
     * Remove the specified learning module.
     */
    public function destroy(LearningModule $module): RedirectResponse
    {
        $title = $module->title;
        $module->delete();

        return redirect()->route('manage.modules.index')->with('success', "Modul '{$title}' telah berhasil dihapus dari sistem.");
    }
}
