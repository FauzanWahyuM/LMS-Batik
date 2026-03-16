<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;

// Landing Page Routes
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/tentang', [LandingController::class, 'about'])->name('landing.about');
Route::get('/program', [LandingController::class, 'programs'])->name('landing.programs');
Route::get('/galeri', [LandingController::class, 'gallery'])->name('landing.gallery');
Route::get('/pendaftaran', [LandingController::class, 'registration'])->name('landing.registration');
Route::post('/pendaftaran/individu', [LandingController::class, 'submitIndividu'])->name('landing.registration.individu');
Route::post('/pendaftaran/kelompok', [LandingController::class, 'submitKelompok'])->name('landing.registration.kelompok');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('simple.auth')->group(function (): void {
	Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.index');

	Route::prefix('/dashboard/peserta')->name('dashboard.participant.')->group(function (): void {
		Route::get('/', [AuthController::class, 'participantHome'])->name('home');
		Route::get('/modul', [AuthController::class, 'participantModules'])->name('modules');
		Route::get('/modul/{module}', [AuthController::class, 'participantModuleDetail'])->name('modules.detail');
		Route::post('/modul/{module}/tugas/upload', [AuthController::class, 'uploadParticipantTask'])->name('modules.tasks.upload');
		Route::get('/forum', [AuthController::class, 'participantForum'])->name('forum');
		Route::get('/galeri', [AuthController::class, 'participantGallery'])->name('gallery');
	});
});
