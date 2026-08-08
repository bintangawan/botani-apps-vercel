<?php

use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DosenDashboardController;
use App\Http\Controllers\Web\MahasiswaDashboardController;
use App\Http\Controllers\Web\ModuleManagementController;
use App\Http\Controllers\Web\PlantManagementController;
use App\Http\Controllers\Web\PublicController;
use App\Http\Controllers\Web\QuestionManagementController;
use App\Http\Controllers\Web\QuizManagementController;
use App\Http\Controllers\Web\StudentQuizController;
use App\Http\Controllers\Web\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Pages (Guest / Everyone)
Route::controller(PublicController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/tumbuhan', 'catalog')->name('catalog');
    Route::get('/tumbuhan/{slug}', 'plantDetail')->name('catalog.detail');
    Route::get('/materi', 'modules')->name('modules');
    Route::get('/materi/{slug}/{lessonSlug?}', 'moduleDetail')->name('modules.detail');
    Route::get('/tentang', 'about')->name('about');
});

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register')->name('register.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Role Protected Dashboard Routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
    Route::post('/quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
    Route::get('/quizzes/attempt/{attempt}', [StudentQuizController::class, 'attempt'])->name('quizzes.attempt');
    Route::post('/quizzes/attempt/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/result/{attempt}', [StudentQuizController::class, 'result'])->name('quizzes.result');
});

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

// Shared Management Routes for Admin & Dosen
Route::middleware(['auth', 'role:admin,dosen'])->prefix('manage')->name('manage.')->group(function () {
    Route::resource('plants', PlantManagementController::class);
    Route::resource('modules', ModuleManagementController::class);
    Route::resource('quizzes', QuizManagementController::class);
    Route::resource('quizzes.questions', QuestionManagementController::class)->except(['show', 'create', 'edit']);
});
