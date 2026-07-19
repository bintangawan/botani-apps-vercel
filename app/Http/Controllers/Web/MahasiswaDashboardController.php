<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\LearningModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MahasiswaDashboardController extends Controller
{
    protected LearningModuleService $moduleService;

    public function __construct(LearningModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function index(): View
    {
        $user = Auth::user();
        $modules = $this->moduleService->getAllPublishedModules();
        $attempts = $user->quizAttempts()->with('quiz.learningModule')->latest()->take(5)->get();

        return view('pages.mahasiswa.dashboard', compact('user', 'modules', 'attempts'));
    }
}
