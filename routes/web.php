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

	Route::prefix('/dashboard/pengelola')->name('dashboard.manager.')->group(function (): void {
		Route::get('/', [AuthController::class, 'managerHome'])->name('home');
		Route::get('/kelola-peserta-individu', [AuthController::class, 'managerIndividualParticipants'])->name('participants.individual');
		Route::post('/kelola-peserta-individu/{participant}/status', [AuthController::class, 'managerIndividualParticipantsUpdate'])->name('participants.individual.update');
		Route::get('/kelola-peserta-kelompok', [AuthController::class, 'managerGroupParticipants'])->name('participants.group');
		Route::post('/kelola-peserta-kelompok/{group}/status', [AuthController::class, 'managerGroupParticipantsUpdate'])->name('participants.group.update');
		Route::get('/kelola-pengajar', [AuthController::class, 'managerInstructors'])->name('instructors');
		Route::post('/kelola-pengajar/{instructor}/status', [AuthController::class, 'managerInstructorsUpdate'])->name('instructors.update');
		Route::get('/kelola-program', [AuthController::class, 'managerPrograms'])->name('programs');
		Route::post('/kelola-program/{program}/status', [AuthController::class, 'managerProgramsUpdate'])->name('programs.update');
		Route::get('/laporan', [AuthController::class, 'managerReports'])->name('reports');
		Route::post('/laporan/export', [AuthController::class, 'managerReportsExport'])->name('reports.export');
		Route::get('/pengaturan', [AuthController::class, 'managerSettings'])->name('settings');
		Route::post('/pengaturan/update', [AuthController::class, 'managerSettingsUpdate'])->name('settings.update');
	});

	Route::prefix('/dashboard/penguji')->name('dashboard.instructor.')->group(function (): void {
		Route::get('/', [AuthController::class, 'instructorHome'])->name('home');
		Route::get('/kelola-modul', [AuthController::class, 'instructorModules'])->name('modules');
		Route::get('/kelola-modul/create', [AuthController::class, 'instructorModulesCreate'])->name('modules.create');
		Route::post('/kelola-modul', [AuthController::class, 'instructorModulesStore'])->name('modules.store');
		Route::get('/kelola-modul/{module}/detail', [AuthController::class, 'instructorModulesDetail'])->name('modules.detail');
		Route::get('/kelola-modul/{module}/edit', [AuthController::class, 'instructorModulesEdit'])->name('modules.edit');
		Route::post('/kelola-modul/{module}', [AuthController::class, 'instructorModulesEditStore'])->name('modules.update');
		Route::delete('/kelola-modul/{module}', [AuthController::class, 'instructorModulesDelete'])->name('modules.delete');
		Route::get('/daftar-peserta', [AuthController::class, 'instructorParticipants'])->name('participants');
		Route::get('/forum-diskusi', [AuthController::class, 'instructorForum'])->name('forum');
		Route::post('/forum-diskusi/{thread}/reply', [AuthController::class, 'instructorForumReply'])->name('forum.reply');
		Route::get('/penilaian-tugas', [AuthController::class, 'instructorAssessments'])->name('assessments');
		Route::post('/penilaian-tugas/{submission}/score', [AuthController::class, 'instructorAssessmentScore'])->name('assessments.score');
	});

	Route::prefix('/dashboard/peserta')->name('dashboard.participant.')->group(function (): void {
		Route::get('/', [AuthController::class, 'participantHome'])->name('home');
		Route::get('/modul', [AuthController::class, 'participantModules'])->name('modules');
		Route::get('/modul/{module}', [AuthController::class, 'participantModuleDetail'])->name('modules.detail');
		Route::post('/modul/{module}/tugas/upload', [AuthController::class, 'uploadParticipantTask'])->name('modules.tasks.upload');
		Route::get('/forum', [AuthController::class, 'participantForum'])->name('forum');
		Route::get('/galeri', [AuthController::class, 'participantGallery'])->name('gallery');
	});
});
