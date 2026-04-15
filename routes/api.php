<?php

use App\Http\Controllers\Api\ForumDiscussionApiController;
use App\Http\Controllers\Api\ParticipantLearningApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['web', 'simple.auth.api'])
    ->group(function (): void {
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
