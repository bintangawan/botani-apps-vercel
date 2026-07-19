<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Morphology;
use App\Models\PlantSpecies;
use App\Models\Taxa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlantManagementController extends Controller
{
    /**
     * Display listing of plant species.
     */
    public function index(Request $request): View
    {
        $query = PlantSpecies::with(['taxa', 'morphology']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('local_name', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group_type')) {
            $query->where('group_type', $request->input('group_type'));
        }

        $plants = $query->latest()->paginate(10)->withQueryString();

        return view('pages.manage.plants.index', compact('plants'));
    }

    /**
     * Show form for creating a new plant species.
     */
    public function create(): View
    {
        return view('pages.manage.plants.create');
    }

    /**
     * Store a newly created plant species along with morphology and taxonomy.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:plant_species'],
            'local_name' => ['required', 'string', 'max:255'],
            'scientific_name' => ['required', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'group_type' => ['required', 'in:Gymnospermae,Angiospermae'],
            'cotyledon_type' => ['nullable', 'in:Monokotil,Dikotil'],
            'description' => ['nullable', 'string'],
            'habitat' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:published,draft'],
            // Morphology
            'root' => ['nullable', 'string'],
            'stem' => ['nullable', 'string'],
            'leaf' => ['nullable', 'string'],
            'flower' => ['nullable', 'string'],
            'fruit' => ['nullable', 'string'],
            'seed' => ['nullable', 'string'],
            'special_characteristics' => ['nullable', 'string'],
            // Taxonomy
            'kingdom' => ['nullable', 'string'],
            'divisi' => ['nullable', 'string'],
            'kelas' => ['nullable', 'string'],
            'ordo' => ['nullable', 'string'],
            'famili' => ['nullable', 'string'],
            'genus' => ['nullable', 'string'],
        ], [
            'code.unique' => 'Kode spesimen ini sudah digunakan oleh tumbuhan lain.',
        ]);

        $slug = Str::slug($validated['local_name'] . '-' . substr($validated['code'], -4));
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('plants', 'public');
        }

        $plant = PlantSpecies::create([
            'code' => strtoupper($validated['code']),
            'slug' => $slug,
            'local_name' => $validated['local_name'],
            'scientific_name' => $validated['scientific_name'],
            'author_name' => $validated['author_name'] ?? null,
            'group_type' => $validated['group_type'],
            'cotyledon_type' => $validated['cotyledon_type'] ?? null,
            'description' => $validated['description'] ?? null,
            'habitat' => $validated['habitat'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'image_path' => $imagePath ?? 'images/plants/default.jpg',
            'status' => $validated['status'],
        ]);

        // Save Morphology
        Morphology::create([
            'species_id' => $plant->id,
            'root' => $validated['root'] ?? null,
            'stem' => $validated['stem'] ?? null,
            'leaf' => $validated['leaf'] ?? null,
            'flower' => $validated['flower'] ?? null,
            'fruit' => $validated['fruit'] ?? null,
            'seed' => $validated['seed'] ?? null,
            'special_characteristics' => $validated['special_characteristics'] ?? null,
        ]);

        // Save & Attach Taxonomy
        $ranks = [
            'kingdom' => $validated['kingdom'] ?? 'Plantae',
            'divisi' => $validated['divisi'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
            'ordo' => $validated['ordo'] ?? null,
            'famili' => $validated['famili'] ?? null,
            'genus' => $validated['genus'] ?? null,
        ];

        foreach ($ranks as $rank => $name) {
            if (!empty($name)) {
                $taxon = Taxa::firstOrCreate([
                    'rank' => $rank,
                    'name' => trim($name),
                ]);
                $plant->taxa()->attach($taxon->id);
            }
        }

        return redirect()->route('manage.plants.index')->with('success', "Spesimen tumbuhan '{$plant->local_name}' berhasil ditambahkan ke katalog!");
    }

    /**
     * Show form for editing the specified plant species.
     */
    public function edit(PlantSpecies $plant): View
    {
        $plant->load(['morphology', 'taxa']);
        
        $taxaMap = [];
        foreach ($plant->taxa as $t) {
            $taxaMap[$t->rank] = $t->name;
        }

        return view('pages.manage.plants.edit', compact('plant', 'taxaMap'));
    }

    /**
     * Update the specified plant species along with morphology and taxonomy.
     */
    public function update(Request $request, PlantSpecies $plant): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:plant_species,code,' . $plant->id],
            'local_name' => ['required', 'string', 'max:255'],
            'scientific_name' => ['required', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'group_type' => ['required', 'in:Gymnospermae,Angiospermae'],
            'cotyledon_type' => ['nullable', 'in:Monokotil,Dikotil'],
            'description' => ['nullable', 'string'],
            'habitat' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:published,draft'],
            // Morphology
            'root' => ['nullable', 'string'],
            'stem' => ['nullable', 'string'],
            'leaf' => ['nullable', 'string'],
            'flower' => ['nullable', 'string'],
            'fruit' => ['nullable', 'string'],
            'seed' => ['nullable', 'string'],
            'special_characteristics' => ['nullable', 'string'],
            // Taxonomy
            'kingdom' => ['nullable', 'string'],
            'divisi' => ['nullable', 'string'],
            'kelas' => ['nullable', 'string'],
            'ordo' => ['nullable', 'string'],
            'famili' => ['nullable', 'string'],
            'genus' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($validated['local_name'] . '-' . substr($validated['code'], -4));
        $imagePath = $plant->image_path;

        if ($request->hasFile('image')) {
            if ($imagePath && !str_contains($imagePath, 'default.jpg') && !str_contains($imagePath, 'images/plants')) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('plants', 'public');
        }

        $plant->update([
            'code' => strtoupper($validated['code']),
            'slug' => $slug,
            'local_name' => $validated['local_name'],
            'scientific_name' => $validated['scientific_name'],
            'author_name' => $validated['author_name'] ?? null,
            'group_type' => $validated['group_type'],
            'cotyledon_type' => $validated['cotyledon_type'] ?? null,
            'description' => $validated['description'] ?? null,
            'habitat' => $validated['habitat'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'image_path' => $imagePath,
            'status' => $validated['status'],
        ]);

        // Update Morphology
        $plant->morphology()->updateOrCreate(
            ['species_id' => $plant->id],
            [
                'root' => $validated['root'] ?? null,
                'stem' => $validated['stem'] ?? null,
                'leaf' => $validated['leaf'] ?? null,
                'flower' => $validated['flower'] ?? null,
                'fruit' => $validated['fruit'] ?? null,
                'seed' => $validated['seed'] ?? null,
                'special_characteristics' => $validated['special_characteristics'] ?? null,
            ]
        );

        // Update Taxonomy
        $ranks = [
            'kingdom' => $validated['kingdom'] ?? 'Plantae',
            'divisi' => $validated['divisi'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
            'ordo' => $validated['ordo'] ?? null,
            'famili' => $validated['famili'] ?? null,
            'genus' => $validated['genus'] ?? null,
        ];

        $taxonIds = [];
        foreach ($ranks as $rank => $name) {
            if (!empty($name)) {
                $taxon = Taxa::firstOrCreate([
                    'rank' => $rank,
                    'name' => trim($name),
                ]);
                $taxonIds[] = $taxon->id;
            }
        }
        $plant->taxa()->sync($taxonIds);

        return redirect()->route('manage.plants.index')->with('success', "Spesimen tumbuhan '{$plant->local_name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified plant species from storage.
     */
    public function destroy(PlantSpecies $plant): RedirectResponse
    {
        $name = $plant->local_name;
        $plant->delete();

        return redirect()->route('manage.plants.index')->with('success', "Spesimen '{$name}' telah berhasil dihapus dari database katalog.");
    }
}
