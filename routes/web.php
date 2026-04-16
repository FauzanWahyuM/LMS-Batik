<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructorModuleController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ParticipantModuleController;
use App\Http\Controllers\ForumController;

Route::get('/files/{path}', function (string $path) {
	if (! Storage::disk('public')->exists($path)) {
		abort(404);
	}

	return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*')->name('public-file');

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
Route::post('/login/forgot-password/request', [AuthController::class, 'requestForgotPasswordCode'])->name('login.forgot-password.request');
Route::post('/login/forgot-password/reset', [AuthController::class, 'resetForgotPassword'])->name('login.forgot-password.reset');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('simple.auth')->group(function (): void {
	Route::post('/forum/store', [ForumController::class, 'store'])->name('forum.store');
	Route::post('/forum/{discussion}/update', [ForumController::class, 'update'])->name('forum.update');
	Route::post('/forum/{discussion}/delete', [ForumController::class, 'delete'])->name('forum.delete');
	Route::post('/forum/{discussion}/toggle-pin', [ForumController::class, 'togglePin'])->name('forum.toggle-pin');
	Route::post('/forum/{discussion}/toggle-close', [ForumController::class, 'toggleClose'])->name('forum.toggle-close');
	Route::post('/forum/{discussion}/reply', [ForumController::class, 'storeReply'])->name('forum.reply');
	Route::post('/forum/reply/{reply}/update', [ForumController::class, 'updateReply'])->name('forum.reply.update');
	Route::post('/forum/reply/{reply}/delete', [ForumController::class, 'deleteReply'])->name('forum.reply.delete');

	Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.index');

	Route::prefix('/dashboard/pengelola')->name('dashboard.manager.')->group(function (): void {
		Route::get('/', [AuthController::class, 'managerHome'])->name('home');
		Route::get('/profil', [AuthController::class, 'managerProfile'])->name('profile');
		Route::post('/profil/update', [AuthController::class, 'managerProfileUpdate'])->name('profile.update');
		Route::get('/kelola-peserta-individu', [AuthController::class, 'managerIndividualParticipants'])->name('participants.individual');
		Route::post('/kelola-peserta-individu/{participant}/generate-credential', [AuthController::class, 'managerIndividualParticipantsGenerateCredential'])->name('participants.individual.generate-credential');
		Route::post('/kelola-peserta-individu/{participant}/send-credential', [AuthController::class, 'managerIndividualParticipantsSendCredential'])->name('participants.individual.send-credential');
		Route::post('/kelola-peserta-individu/{participant}/status', [AuthController::class, 'managerIndividualParticipantsUpdate'])->name('participants.individual.update');
		Route::get('/kelola-peserta-kelompok', [AuthController::class, 'managerGroupParticipants'])->name('participants.group');
		Route::post('/kelola-peserta-kelompok/{group}/generate-credential', [AuthController::class, 'managerGroupParticipantsGenerateCredential'])->name('participants.group.generate-credential');
		Route::post('/kelola-peserta-kelompok/{group}/send-credential', [AuthController::class, 'managerGroupParticipantsSendCredential'])->name('participants.group.send-credential');
		Route::get('/kelola-peserta-kelompok/{group}/download-export', [AuthController::class, 'managerGroupParticipantsDownloadCredentialExport'])->name('participants.group.download-export');
		Route::post('/kelola-peserta-kelompok/{group}/status', [AuthController::class, 'managerGroupParticipantsUpdate'])->name('participants.group.update');
		Route::get('/kelola-pengajar', [AuthController::class, 'managerInstructors'])->name('instructors');
		Route::post('/kelola-pengajar/create', [AuthController::class, 'managerInstructorsStore'])->name('instructors.store');
		Route::post('/kelola-pengajar/{instructor}/status', [AuthController::class, 'managerInstructorsUpdate'])->name('instructors.update');
		Route::post('/kelola-pengajar/{instructor}/edit', [AuthController::class, 'managerInstructorsEdit'])->name('instructors.edit');
		Route::post('/kelola-pengajar/{instructor}/delete', [AuthController::class, 'managerInstructorsDelete'])->name('instructors.delete');
		Route::get('/kelola-program', [AuthController::class, 'managerPrograms'])->name('programs');
		Route::post('/kelola-program/create', [AuthController::class, 'managerProgramsStore'])->name('programs.store');
		Route::post('/kelola-program/{program}/edit', [AuthController::class, 'managerProgramsEdit'])->name('programs.edit');
		Route::post('/kelola-program/{program}/delete', [AuthController::class, 'managerProgramsDelete'])->name('programs.delete');
		Route::post('/kelola-program/{program}/status', [AuthController::class, 'managerProgramsUpdate'])->name('programs.update');
		Route::get('/laporan', [AuthController::class, 'managerReports'])->name('reports');
		Route::post('/laporan/export', [AuthController::class, 'managerReportsExport'])->name('reports.export');
		Route::get('/kelola-prestasi', [AuthController::class, 'managerAchievements'])->name('achievements');
		Route::post('/kelola-prestasi/create', [AuthController::class, 'managerAchievementsStore'])->name('achievements.store');
		Route::post('/kelola-prestasi/{achievement}/edit', [AuthController::class, 'managerAchievementsEdit'])->name('achievements.edit');
		Route::post('/kelola-prestasi/{achievement}/delete', [AuthController::class, 'managerAchievementsDelete'])->name('achievements.delete');
		Route::get('/kelola-testimoni', [AuthController::class, 'managerTestimonials'])->name('testimonials');
		Route::post('/kelola-testimoni/create', [AuthController::class, 'managerTestimonialsStore'])->name('testimonials.store');
		Route::post('/kelola-testimoni/{testimonial}/edit', [AuthController::class, 'managerTestimonialsEdit'])->name('testimonials.edit');
		Route::post('/kelola-testimoni/{testimonial}/delete', [AuthController::class, 'managerTestimonialsDelete'])->name('testimonials.delete');
		Route::get('/kelola-fasilitas', [AuthController::class, 'managerFacilities'])->name('facilities');
		Route::post('/kelola-fasilitas/create', [AuthController::class, 'managerFacilitiesStore'])->name('facilities.store');
		Route::post('/kelola-fasilitas/{facility}/edit', [AuthController::class, 'managerFacilitiesEdit'])->name('facilities.edit');
		Route::post('/kelola-fasilitas/{facility}/delete', [AuthController::class, 'managerFacilitiesDelete'])->name('facilities.delete');
		Route::get('/kelola-mitra', [AuthController::class, 'managerPartners'])->name('partners');
		Route::post('/kelola-mitra/create', [AuthController::class, 'managerPartnersStore'])->name('partners.store');
		Route::post('/kelola-mitra/{partner}/edit', [AuthController::class, 'managerPartnersEdit'])->name('partners.edit');
		Route::post('/kelola-mitra/{partner}/delete', [AuthController::class, 'managerPartnersDelete'])->name('partners.delete');
		Route::get('/pengaturan', [AuthController::class, 'managerSettings'])->name('settings');
		Route::post('/pengaturan/update', [AuthController::class, 'managerSettingsUpdate'])->name('settings.update');
	});

	Route::prefix('/dashboard/penguji')->name('dashboard.instructor.')->group(function (): void {
		Route::get('/', [AuthController::class, 'instructorHome'])->name('home');
		Route::get('/profil', [AuthController::class, 'instructorProfile'])->name('profile');
		Route::post('/profil/update', [AuthController::class, 'instructorProfileUpdate'])->name('profile.update');
		Route::get('/kelola-modul', [InstructorModuleController::class, 'index'])->name('modules');
		Route::get('/kelola-modul/create', [InstructorModuleController::class, 'create'])->name('modules.create');
		Route::post('/kelola-modul', [InstructorModuleController::class, 'store'])->name('modules.store');
		Route::post('/kelola-modul/upload-content-image', [InstructorModuleController::class, 'uploadChapterImage'])->name('modules.content-image.upload');
		Route::get('/kelola-modul/{module}/detail', [InstructorModuleController::class, 'show'])->name('modules.detail');
		Route::get('/kelola-modul/{module}/edit', [InstructorModuleController::class, 'edit'])->name('modules.edit');
		Route::put('/kelola-modul/{module}', [InstructorModuleController::class, 'update'])->name('modules.update');
		Route::delete('/kelola-modul/{module}', [InstructorModuleController::class, 'destroy'])->name('modules.delete');
		Route::get('/daftar-peserta', [AuthController::class, 'instructorParticipants'])->name('participants');
		Route::get('/daftar-peserta/individu', [AuthController::class, 'instructorParticipantsIndividualDetail'])->name('participants.individual.detail');
		Route::get('/daftar-peserta/kelompok', [AuthController::class, 'instructorParticipantsGroupDetail'])->name('participants.group.detail');
		Route::get('/forum-diskusi', [AuthController::class, 'instructorForum'])->name('forum');
		Route::get('/penilaian-tugas', [AuthController::class, 'instructorAssessments'])->name('assessments');
		Route::get('/penilaian-tugas/{submission}/detail', [AuthController::class, 'instructorAssessmentsDetail'])->name('assessments.detail');
		Route::post('/penilaian-tugas/{submission}/score', [AuthController::class, 'instructorAssessmentScore'])->name('assessments.score');
	});

	Route::prefix('/dashboard/peserta')->name('dashboard.participant.')->middleware('force.password.change')->group(function (): void {
		Route::get('/', [AuthController::class, 'participantHome'])->name('home');
		Route::get('/profil', [AuthController::class, 'participantProfile'])->name('profile');
		Route::post('/profil/update', [AuthController::class, 'participantProfileUpdate'])->name('profile.update');
		Route::get('/modul', [ParticipantModuleController::class, 'index'])->name('modules');
		Route::get('/modul/{module}', [ParticipantModuleController::class, 'show'])->name('modules.detail');
		Route::post('/modul/{module}/tugas/upload', [ParticipantModuleController::class, 'submitAssignment'])->name('modules.tasks.upload');
		Route::get('/modul/{module}/progress', [ParticipantModuleController::class, 'getProgress'])->name('modules.progress');
		Route::post('/modul/{module}/material/{material}/start', [ParticipantModuleController::class, 'markMaterialStarted'])->name('modules.material.start');
		Route::post('/modul/{module}/material/{material}/read', [ParticipantModuleController::class, 'markMaterialRead'])->name('modules.material.read');
		Route::post('/modul/{module}/material/{material}/watch', [ParticipantModuleController::class, 'markMaterialWatched'])->name('modules.material.watch');
		Route::post('/modul/{module}/material/{material}/complete', [ParticipantModuleController::class, 'markMaterialCompleted'])->name('modules.material.complete');
		Route::get('/forum', [AuthController::class, 'participantForum'])->name('forum');
		Route::get('/galeri', [AuthController::class, 'participantGallery'])->name('gallery');
		Route::get('/galeri/upload', [AuthController::class, 'participantGalleryUpload'])->name('gallery.upload');
		Route::post('/galeri/upload', [AuthController::class, 'participantGalleryStore'])->name('gallery.store');
	});
});
