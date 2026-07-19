<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\PlantObservation;
use App\Models\PlantSpecies;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_species' => PlantSpecies::count(),
            'total_observations' => PlantObservation::count(),
            'total_modules' => LearningModule::count(),
            'total_users' => User::count(),
        ];

        $recentSpecies = PlantSpecies::latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('pages.admin.dashboard', compact('stats', 'recentSpecies', 'recentUsers'));
    }
}
