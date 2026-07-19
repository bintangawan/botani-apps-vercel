<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PlantSpecies;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\View\View;

class DosenDashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = User::where('role', 'mahasiswa')->count();
        $totalAttempts = QuizAttempt::count();
        $avgScore = QuizAttempt::avg('score') ?? 0;
        $recentAttempts = QuizAttempt::with(['user', 'quiz.learningModule'])->latest()->take(10)->get();

        return view('pages.dosen.dashboard', compact('totalStudents', 'totalAttempts', 'avgScore', 'recentAttempts'));
    }
}
