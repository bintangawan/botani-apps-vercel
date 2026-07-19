<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizManagementController extends Controller
{
    /**
     * Display a listing of quizzes.
     */
    public function index(Request $request): View
    {
        $query = Quiz::with('learningModule')->withCount(['questions', 'attempts']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->input('module_id'));
        }

        $quizzes = $query->latest()->paginate(10)->withQueryString();
        $modules = LearningModule::orderBy('module_order', 'asc')->get();

        return view('pages.manage.quizzes.index', compact('quizzes', 'modules'));
    }

    /**
     * Show form for creating a new quiz.
     */
    public function create(): View
    {
        $modules = LearningModule::orderBy('module_order', 'asc')->get();
        return view('pages.manage.quizzes.create', compact('modules'));
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module_id' => ['required', 'exists:learning_modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'quiz_type' => ['required', 'in:pilihan_ganda,esai,campuran'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'duration' => ['required', 'integer', 'min:1', 'max:360'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $quiz = Quiz::create($validated);

        return redirect()->route('manage.quizzes.questions.index', $quiz)->with('success', "Kuis evaluasi '{$quiz->title}' berhasil dibuat! Sekarang Anda dapat menyusun Bank Soal & Kunci Jawaban.");
    }

    /**
     * Show form for editing the specified quiz.
     */
    public function edit(Quiz $quiz): View
    {
        $modules = LearningModule::orderBy('module_order', 'asc')->get();
        return view('pages.manage.quizzes.edit', compact('quiz', 'modules'));
    }

    /**
     * Update the specified quiz.
     */
    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'module_id' => ['required', 'exists:learning_modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'quiz_type' => ['required', 'in:pilihan_ganda,esai,campuran'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'duration' => ['required', 'integer', 'min:1', 'max:360'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $quiz->update($validated);

        return redirect()->route('manage.quizzes.index')->with('success', "Konfigurasi kuis '{$quiz->title}' berhasil diperbarui!");
    }

    /**
     * Remove the specified quiz.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $title = $quiz->title;
        $quiz->delete();

        return redirect()->route('manage.quizzes.index')->with('success', "Kuis '{$title}' dan seluruh bank soal terkait telah berhasil dihapus.");
    }
}
