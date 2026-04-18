<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ForumDiscussionApiController;
use App\Http\Controllers\Api\InstructorModuleApiController;
use App\Http\Controllers\Api\LandingRegistrationApiController;
use App\Http\Controllers\Api\ManagerTestimonialApiController;
use App\Http\Controllers\Api\ManagerUserApiController;
use App\Http\Controllers\Api\ParticipantLearningApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['web'])
    ->group(function (): void {
        Route::prefix('auth')->name('api.v1.auth.')->group(function (): void {
            Route::post('/login', [AuthApiController::class, 'login'])->name('login');
            Route::post('/forgot-password/request', [AuthApiController::class, 'requestForgotPasswordCode'])->name('forgot-password.request');
            Route::post('/forgot-password/reset', [AuthApiController::class, 'resetForgotPassword'])->name('forgot-password.reset');
        });

        Route::prefix('registrations')->name('api.v1.registrations.')->group(function (): void {
            Route::post('/individual', [LandingRegistrationApiController::class, 'submitIndividu'])->name('individual.store');
            Route::post('/group', [LandingRegistrationApiController::class, 'submitKelompok'])->name('group.store');
        });

        Route::middleware(['simple.auth.api'])->group(function (): void {
            Route::prefix('auth')->name('api.v1.auth.')->group(function (): void {
                Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');
            });

            Route::prefix('instructor/modules')->name('api.v1.instructor.modules.')->group(function (): void {
                Route::get('/', [InstructorModuleApiController::class, 'index'])->name('index');
                Route::post('/', [InstructorModuleApiController::class, 'store'])->name('store');
                Route::post('/content-image', [InstructorModuleApiController::class, 'uploadChapterImage'])->name('content-image.upload');
                Route::get('/{module}', [InstructorModuleApiController::class, 'show'])->name('show');
                Route::put('/{module}', [InstructorModuleApiController::class, 'update'])->name('update');
                Route::patch('/{module}', [InstructorModuleApiController::class, 'update'])->name('patch');
                Route::delete('/{module}', [InstructorModuleApiController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('manager/users')->name('api.v1.manager.users.')->group(function (): void {
                Route::get('/', [ManagerUserApiController::class, 'index'])->name('index');
                Route::post('/', [ManagerUserApiController::class, 'store'])->name('store');
                Route::put('/{user}', [ManagerUserApiController::class, 'update'])->name('update');
                Route::patch('/{user}', [ManagerUserApiController::class, 'update'])->name('patch');
                Route::patch('/{user}/status', [ManagerUserApiController::class, 'updateStatus'])->name('status.update');
                Route::delete('/{user}', [ManagerUserApiController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('manager/testimonials')->name('api.v1.manager.testimonials.')->group(function (): void {
                Route::get('/', [ManagerTestimonialApiController::class, 'index'])->name('index');
                Route::post('/', [ManagerTestimonialApiController::class, 'store'])->name('store');
                Route::put('/{testimonial}', [ManagerTestimonialApiController::class, 'update'])->name('update');
                Route::patch('/{testimonial}', [ManagerTestimonialApiController::class, 'update'])->name('patch');
                Route::delete('/{testimonial}', [ManagerTestimonialApiController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('forum')->name('api.v1.forum.')->group(function (): void {
            Route::get('/modules', [ForumDiscussionApiController::class, 'modules'])->name('modules.index');
            Route::get('/discussions', [ForumDiscussionApiController::class, 'index'])->name('discussions.index');
            Route::post('/discussions', [ForumDiscussionApiController::class, 'store'])->name('discussions.store');
            Route::put('/discussions/{discussion}', [ForumDiscussionApiController::class, 'update'])->name('discussions.update');
            Route::delete('/discussions/{discussion}', [ForumDiscussionApiController::class, 'destroy'])->name('discussions.destroy');
            Route::put('/discussions/{discussion}/pin', [ForumDiscussionApiController::class, 'togglePin'])->name('discussions.pin');
            Route::put('/discussions/{discussion}/close', [ForumDiscussionApiController::class, 'toggleClose'])->name('discussions.close');
            Route::post('/discussions/{discussion}/replies', [ForumDiscussionApiController::class, 'storeReply'])->name('replies.store');
            Route::put('/replies/{reply}', [ForumDiscussionApiController::class, 'updateReply'])->name('replies.update');
            Route::delete('/replies/{reply}', [ForumDiscussionApiController::class, 'destroyReply'])->name('replies.destroy');
        });

        Route::prefix('participant/modules')->name('api.v1.participant.modules.')->group(function (): void {
            Route::get('/', [ParticipantLearningApiController::class, 'index'])->name('index');
            Route::get('/{moduleSlug}', [ParticipantLearningApiController::class, 'show'])->name('show');
            Route::get('/{moduleSlug}/progress', [ParticipantLearningApiController::class, 'progress'])->name('progress');
            Route::post('/{moduleSlug}/materials/{materialSlug}/start', [ParticipantLearningApiController::class, 'startMaterial'])->name('materials.start');
            Route::put('/{moduleSlug}/materials/{materialSlug}/read', [ParticipantLearningApiController::class, 'markMaterialRead'])->name('materials.read');
            Route::put('/{moduleSlug}/materials/{materialSlug}/watch', [ParticipantLearningApiController::class, 'markMaterialWatched'])->name('materials.watch');
            Route::post('/{moduleSlug}/assignments', [ParticipantLearningApiController::class, 'uploadAssignment'])->name('assignments.upload');
        });
        });
    });
