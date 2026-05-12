<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\InstructorModuleController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ParticipantModuleController;

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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', fn () => redirect()->route('login'))->name('register');

Route::middleware('simple.auth')->group(function (): void {
	Route::post('/forum/store', [ForumController::class, 'store'])->name('forum.store');
	Route::post('/forum/{discussion}/update', [ForumController::class, 'update'])->name('forum.update');
	Route::post('/forum/{discussion}/delete', [ForumController::class, 'delete'])->name('forum.delete');
	Route::post('/forum/{discussion}/toggle-pin', [ForumController::class, 'togglePin'])->name('forum.toggle-pin');
	Route::post('/forum/{discussion}/toggle-close', [ForumController::class, 'toggleClose'])->name('forum.toggle-close');
	Route::post('/forum/{discussion}/reply', [ForumController::class, 'storeReply'])->name('forum.reply');
	Route::post('/forum/reply/{reply}/update', [ForumController::class, 'updateReply'])->name('forum.reply.update');
	Route::post('/forum/reply/{reply}/delete', [ForumController::class, 'deleteReply'])->name('forum.reply.delete');

	Route::post('/forum/module-discussions/store', [ForumController::class, 'storeModuleDiscussion'])->name('forum.module-discussion.store');
	Route::post('/forum/module-discussions/{discussion}/reply', [ForumController::class, 'storeModuleReply'])->name('forum.module-discussion.reply');
	Route::post('/forum/module-discussions/{discussion}/update', [ForumController::class, 'updateModuleDiscussion'])->name('forum.module-discussion.update');
	Route::post('/forum/module-discussions/{discussion}/delete', [ForumController::class, 'deleteModuleDiscussion'])->name('forum.module-discussion.delete');
	Route::post('/forum/module-replies/{reply}/update', [ForumController::class, 'updateModuleReply'])->name('forum.module-reply.update');
	Route::post('/forum/module-replies/{reply}/delete', [ForumController::class, 'deleteModuleReply'])->name('forum.module-reply.delete');

	Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.index');

	Route::prefix('/dashboard/pengelola')->name('dashboard.manager.')->group(function (): void {
		Route::get('/', [AuthController::class, 'managerHome'])->name('home');
		Route::get('/profil', [AuthController::class, 'managerProfile'])->name('profile');
		Route::post('/profil/update', [AuthController::class, 'managerProfileUpdate'])->name('profile.update');
		Route::get('/kelola-peserta-individu', [AuthController::class, 'managerIndividualParticipants'])->name('participants.individual');
		Route::post('/kelola-peserta-individu/{participant}/generate-credential', [AuthController::class, 'managerIndividualParticipantsGenerateCredential'])->name('participants.individual.generate-credential');
		Route::post('/kelola-peserta-individu/{participant}/reject', [AuthController::class, 'managerIndividualParticipantsReject'])->name('participants.individual.reject');
		Route::post('/kelola-peserta-individu/{participant}/send-credential', [AuthController::class, 'managerIndividualParticipantsSendCredential'])->name('participants.individual.send-credential');
		Route::post('/kelola-peserta-individu/{participant}/status', [AuthController::class, 'managerIndividualParticipantsUpdate'])->name('participants.individual.update');
		Route::get('/kelola-peserta-kelompok', [AuthController::class, 'managerGroupParticipants'])->name('participants.group');
		Route::get('/kelola-peserta-kelompok/{group}/download-export', [AuthController::class, 'managerGroupParticipantsDownloadCredentialExport'])->name('participants.group.download-export');
		Route::post('/kelola-peserta-kelompok/{group}/generate-credential', [AuthController::class, 'managerGroupParticipantsGenerateCredential'])->name('participants.group.generate-credential');
		Route::post('/kelola-peserta-kelompok/{group}/reject', [AuthController::class, 'managerGroupParticipantsReject'])->name('participants.group.reject');
		Route::post('/kelola-peserta-kelompok/{group}/send-credential', [AuthController::class, 'managerGroupParticipantsSendCredential'])->name('participants.group.send-credential');
		Route::post('/kelola-peserta-kelompok/{group}/status', [AuthController::class, 'managerGroupParticipantsUpdate'])->name('participants.group.update');
		Route::get('/kelola-pengajar', [AuthController::class, 'managerInstructors'])->name('instructors');
		Route::post('/kelola-pengajar/create', [AuthController::class, 'managerInstructorsStore'])->name('instructors.store');
		Route::post('/kelola-pengajar/{instructor}/edit', [AuthController::class, 'managerInstructorsEdit'])->name('instructors.edit');
		Route::get('/kelola-program', [AuthController::class, 'managerPrograms'])->name('programs');
		Route::post('/kelola-program/create', [AuthController::class, 'managerProgramsStore'])->name('programs.store');
		Route::post('/kelola-program/{program}/edit', [AuthController::class, 'managerProgramsEdit'])->name('programs.edit');
		Route::post('/kelola-program/{program}/delete', [AuthController::class, 'managerProgramsDelete'])->name('programs.delete');
		Route::get('/laporan', [AuthController::class, 'managerReports'])->name('reports');
		Route::post('/laporan/export', [AuthController::class, 'managerReportsExport'])->name('reports.export');
		Route::get('/kelola-prestasi', [AuthController::class, 'managerAchievements'])->name('achievements');
		Route::post('/kelola-prestasi/create', [AuthController::class, 'managerAchievementsStore'])->name('achievements.store');
		Route::post('/kelola-prestasi/{achievement}/edit', [AuthController::class, 'managerAchievementsEdit'])->name('achievements.edit');
		Route::post('/kelola-prestasi/{achievement}/delete', [AuthController::class, 'managerAchievementsDelete'])->name('achievements.delete');
		Route::get('/kelola-testimoni', [AuthController::class, 'managerTestimonials'])->name('testimonials');
		Route::get('/kelola-fasilitas', [AuthController::class, 'managerFacilities'])->name('facilities');
		Route::post('/kelola-fasilitas/create', [AuthController::class, 'managerFacilitiesStore'])->name('facilities.store');
		Route::post('/kelola-fasilitas/{facility}/edit', [AuthController::class, 'managerFacilitiesEdit'])->name('facilities.edit');
		Route::post('/kelola-fasilitas/{facility}/delete', [AuthController::class, 'managerFacilitiesDelete'])->name('facilities.delete');
		Route::get('/kelola-gudang', [AuthController::class, 'managerStorage'])->name('storage');
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
		Route::get('/forum', [AuthController::class, 'participantForum'])->name('forum');
		Route::get('/galeri', [AuthController::class, 'participantGallery'])->name('gallery');
		Route::get('/galeri/upload', [AuthController::class, 'participantGalleryUpload'])->name('gallery.upload');
		Route::post('/galeri/upload', [AuthController::class, 'participantGalleryStore'])->name('gallery.store');
	});
});
