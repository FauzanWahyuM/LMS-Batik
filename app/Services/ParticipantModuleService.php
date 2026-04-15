<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleMaterial;
use App\Models\ParticipantAssignment;
use App\Models\ParticipantProgress;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParticipantModuleService
{
    /**
     * Get all modules available for participants
     */
    public function getAvailableModules()
    {
        return Module::with('materials')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get chapter and material content for a module
     */
    public function getMaterialsForModule(Module $module)
    {
        $materials = $module->materials()->orderBy('order')->get();

        if ($materials->isNotEmpty()) {
            return $materials;
        }

        return collect($module->chapters ?? [])->map(function ($chapter, $index) {
            $slug = isset($chapter['title']) ? Str::slug($chapter['title']) : 'chapter-' . ($index + 1);
            $videoUrl = $this->normalizeVideoUrl($chapter['video'] ?? null);
            $images = $chapter['images'] ?? [];

            if (!is_array($images)) {
                $images = [];
            }

            if (empty($images) && !empty($chapter['image_path'])) {
                $images = [[
                    'path' => $chapter['image_path'],
                    'title' => $chapter['image_title'] ?? '',
                    'caption' => $chapter['image_caption'] ?? '',
                    'width' => $chapter['image_width'] ?? 75,
                ]];
            }

            $normalizedImages = array_map(function (array $image): array {
                return [
                    'path' => $image['path'] ?? $image['existing_path'] ?? null,
                    'title' => $image['title'] ?? $image['image_title'] ?? '',
                    'caption' => $image['caption'] ?? $image['image_caption'] ?? '',
                    'width' => $image['width'] ?? $image['image_width'] ?? 75,
                ];
            }, $images);

            return (object) [
                'title' => $chapter['title'] ?? 'Bab ' . ($index + 1),
                'slug' => $slug . '-' . ($index + 1),
                'content' => normalize_uploaded_content_html($chapter['content'] ?? $chapter['description'] ?? null),
                'description' => $chapter['description'] ?? null,
                'video_url' => $videoUrl,
                'assignment' => $chapter['assignment'] ?? null,
                'type' => ! empty($chapter['assignment']) ? 'assignment' : (! empty($videoUrl) ? 'video' : 'content'),
                'metadata' => [
                    'deadline' => $chapter['assignment_deadline'] ?? null,
                    'images' => $normalizedImages,
                    'image_path' => $normalizedImages[0]['path'] ?? null,
                    'image_title' => $normalizedImages[0]['title'] ?? null,
                    'image_caption' => $normalizedImages[0]['caption'] ?? null,
                    'image_width' => $normalizedImages[0]['width'] ?? 75,
                ],
                'thumbnail_url' => null,
                'order' => $index + 1,
            ];
        });
    }

    private function normalizeVideoUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return route('public-file', ['path' => ltrim($url, '/')]);
        }

        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $url;
    }

    /**
     * Get module details with materials for a participant
     */
    public function getModuleForParticipant(Module $module, User $user)
    {
        $materials = $this->getMaterialsForModule($module);
        $progress = $this->getModuleProgress($module, $user);
        $assignments = $this->getUserAssignments($module, $user);
        $taskInstructions = $this->getTaskInstructions($module);
        $completionState = $this->buildModuleCompletionState($module, $user, $materials);

        $materials = $materials->map(function ($material) use ($completionState) {
            $materialState = $completionState['material_states'][$this->getMaterialProgressKey($material)] ?? [];

            $material->is_read = (bool) ($materialState['is_read'] ?? false);
            $material->is_video_watched = (bool) ($materialState['is_video_watched'] ?? false);
            $material->has_video = (bool) ($materialState['has_video'] ?? false);
            $material->is_assignment_submitted = (bool) ($materialState['is_assignment_submitted'] ?? false);
            $material->parts_total = (int) ($materialState['parts_total'] ?? 0);
            $material->parts_completed = (int) ($materialState['parts_completed'] ?? 0);
            $material->completion_percentage = (int) ($materialState['completion_percentage'] ?? 0);
            $material->is_completed = (bool) ($materialState['is_completed'] ?? false);

            return $material;
        });

        return [
            'module' => $module,
            'materials' => $materials,
            'progress' => $progress,
            'assignments' => $assignments,
            'task_instructions' => $taskInstructions,
            'completed_count' => $completionState['completed_materials'],
            'total_count' => $completionState['total_materials'],
            'overall_progress' => $completionState['overall_percentage'],
        ];
    }

    public function getTaskInstructions(Module $module)
    {
        if ($module->materials()->exists()) {
            return $module->materials()
                ->where('type', 'assignment')
                ->orderBy('order')
                ->get(['id', 'slug', 'title', 'metadata'])
                ->map(function (ModuleMaterial $material) {
                    $metadata = is_array($material->metadata) ? $material->metadata : [];

                    return (object) [
                        'id' => $material->id,
                        'slug' => $material->slug,
                        'title' => $material->title,
                        'assignment' => (string) ($metadata['assignment'] ?? ''),
                        'deadline' => $metadata['deadline'] ?? null,
                    ];
                })
                ->values();
        }

        return collect($module->chapters ?? [])
            ->map(function (array $chapter, int $index) {
                $assignment = trim((string) ($chapter['assignment'] ?? ''));

                if ($assignment === '') {
                    return null;
                }

                return (object) [
                    'id' => null,
                    'slug' => Str::slug((string) ($chapter['title'] ?? 'bab-' . ($index + 1))) . '-' . ($index + 1),
                    'title' => (string) ($chapter['title'] ?? ('Bab ' . ($index + 1))),
                    'assignment' => $assignment,
                    'deadline' => $chapter['assignment_deadline'] ?? null,
                ];
            })
            ->filter()
            ->values();
    }

    public function getProgressResponse(Module $module, User $user): array
    {
        $materials = $this->getMaterialsForModule($module);
        $completionState = $this->buildModuleCompletionState($module, $user, $materials);

        return [
            'completed' => $completionState['completed_materials'],
            'total' => $completionState['total_materials'],
            'percentage' => $completionState['overall_percentage'],
        ];
    }

    /**
     * Find a module by slug
     */
    public function findModuleBySlug(string $slug): ?Module
    {
        return Module::with('materials')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get module details for a participant by slug
     */
    public function getModuleForParticipantBySlug(string $slug, User $user): ?array
    {
        $module = $this->findModuleBySlug($slug);

        if (! $module) {
            return null;
        }

        return $this->getModuleForParticipant($module, $user);
    }

    /**
     * Get progress for a specific module and user
     */
    public function getModuleProgress(Module $module, User $user): ?ParticipantProgress
    {
        return $module->getProgressForUser($user);
    }

    /**
     * Get assignments submitted by user for a module
     */
    public function getUserAssignments(Module $module, User $user)
    {
        return $module->getAssignmentsForUser($user);
    }

    /**
     * Calculate overall progress percentage for a module
     */
    public function calculateOverallProgress(Module $module, User $user): int
    {
        $materials = $this->getMaterialsForModule($module);

        return $this->buildModuleCompletionState($module, $user, $materials)['overall_percentage'];
    }

    public function markMaterialAsRead(Module $module, $material, User $user): ParticipantProgress
    {
        $progress = $this->resolveOrCreateMaterialProgress($module, $material, $user);
        $metadata = is_array($progress->metadata) ? $progress->metadata : [];

        $metadata['material_read'] = true;

        $progress->fill([
            'status' => 'completed',
            'progress_percentage' => 100,
            'started_at' => $progress->started_at ?: now(),
            'completed_at' => $progress->completed_at ?: now(),
            'metadata' => $metadata,
        ]);

        $progress->save();

        return $progress;
    }

    public function markMaterialVideoAsWatched(Module $module, $material, User $user): ParticipantProgress
    {
        $progress = $this->resolveOrCreateMaterialProgress($module, $material, $user);
        $metadata = is_array($progress->metadata) ? $progress->metadata : [];

        $metadata['video_watched'] = true;

        $progress->fill([
            'status' => $progress->status === 'not_started' ? 'in_progress' : ($progress->status ?: 'in_progress'),
            'started_at' => $progress->started_at ?: now(),
            'metadata' => $metadata,
        ]);

        $progress->save();

        return $progress;
    }

    public function getMaterialStateForUser(Module $module, $material, User $user): array
    {
        $materials = collect([$material]);
        $completionState = $this->buildModuleCompletionState($module, $user, $materials);

        return $completionState['material_states'][$this->getMaterialProgressKey($material)] ?? [];
    }

    /**
     * Mark material as started for a user
     */
    public function markMaterialAsStarted(Module $module, ModuleMaterial $material, User $user): ParticipantProgress
    {
        return ParticipantProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'material_id' => $material->id,
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );
    }

    /**
     * Mark material as completed for a user
     */
    public function toggleMaterialCompletion(Module $module, ModuleMaterial $material, User $user): ParticipantProgress
    {
        $progress = ParticipantProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'material_id' => $material->id,
            ],
            [
                'status' => 'in_progress',
            ]
        );

        if ($progress->status === 'completed') {
            // ❌ kalau sudah selesai → balikin
            $progress->status = 'in_progress';
            $progress->progress_percentage = 0;
            $progress->completed_at = null;
        } else {
            // ✅ kalau belum → tandai selesai
            $progress->status = 'completed';
            $progress->progress_percentage = 100;
            $progress->completed_at = now();
        }

        $progress->save();

        return $progress;
    }

    /**
     * Submit assignment for a module material
     */
    public function submitAssignment(Module $module, ?ModuleMaterial $material, User $user, UploadedFile $file, ?string $materialSlug = null): ParticipantAssignment
    {
        // Store the file
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('assignments', $fileName, 'public');

        $resolvedMaterialSlug = $materialSlug ?: $material?->slug;

        $existingQuery = ParticipantAssignment::where('user_id', $user->id)
            ->where('module_id', $module->id);

        if ($material?->id) {
            $existingQuery->where('material_id', $material->id);
        } else {
            $existingQuery->whereNull('material_id')
                ->where('material_slug', $resolvedMaterialSlug);
        }

        $existing = $existingQuery->first();

        if ($existing && $existing->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $attributes = [
            'user_id' => $user->id,
            'module_id' => $module->id,
            'material_id' => $material?->id,
        ];

        if (! $material?->id) {
            $attributes['material_slug'] = $resolvedMaterialSlug;
        }

        return ParticipantAssignment::updateOrCreate(
            $attributes,
            [
                'material_slug' => $resolvedMaterialSlug,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'submitted_at' => now(),
            ]
        );
    }

    /**
     * Get assignment for grading (for instructors)
     */
    public function getAssignmentForGrading(int $assignmentId): ?ParticipantAssignment
    {
        return ParticipantAssignment::with(['user', 'module', 'material'])->find($assignmentId);
    }

    /**
     * Grade an assignment
     */
    public function gradeAssignment(ParticipantAssignment $assignment, int $score, ?string $feedback): bool
    {
        return $assignment->update([
            'score' => $score,
            'feedback' => $feedback,
            'graded_at' => now(),
        ]);
    }

    /**
     * Get module statistics for a user
     */
    public function getModuleStatistics(Module $module, User $user): array
    {
        $materials = $this->getMaterialsForModule($module);
        $completionState = $this->buildModuleCompletionState($module, $user, $materials);

        $completedMaterials = $completionState['completed_materials'];
        $totalAssignments = 0;
        $gradedAssignments = 0;

        foreach ($materials as $material) {
            if ($this->materialHasAssignmentPart($material)) {
                $totalAssignments++;
                $assignmentQuery = ParticipantAssignment::where('user_id', $user->id)
                    ->where('module_id', $module->id);

                if (isset($material->id)) {
                    $assignmentQuery->where('material_id', $material->id);
                } else {
                    $assignmentQuery->whereNull('material_id')
                        ->where('material_slug', $material->slug);
                }

                $assignment = $assignmentQuery->first();

                if ($assignment && $assignment->isGraded()) {
                    $gradedAssignments++;
                }
            }
        }

        return [
            'total_materials' => $materials->count(),
            'completed_materials' => $completedMaterials,
            'completion_percentage' => $completionState['overall_percentage'],
            'total_assignments' => $totalAssignments,
            'graded_assignments' => $gradedAssignments,
        ];
    }

    private function buildModuleCompletionState(Module $module, User $user, $materials): array
    {
        $materials = collect($materials)->values();
        $totalMaterials = $materials->count();

        if ($totalMaterials === 0) {
            return [
                'completed_materials' => 0,
                'total_materials' => 0,
                'overall_percentage' => 0,
                'material_states' => [],
            ];
        }

        $materialIds = $materials->pluck('id')->filter()->values()->all();
        $materialSlugs = $materials->pluck('slug')->filter()->values()->all();

        $progressRows = ParticipantProgress::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where(function ($query) use ($materialIds, $materialSlugs) {
                if (! empty($materialIds)) {
                    $query->whereIn('material_id', $materialIds);
                }

                if (! empty($materialSlugs)) {
                    $query->orWhere(function ($slugQuery) use ($materialSlugs) {
                        $slugQuery->whereNull('material_id')
                            ->whereIn('material_slug', $materialSlugs);
                    });
                }
            })
            ->get();

        $assignmentRows = ParticipantAssignment::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where(function ($query) use ($materialIds, $materialSlugs) {
                if (! empty($materialIds)) {
                    $query->whereIn('material_id', $materialIds);
                }

                if (! empty($materialSlugs)) {
                    $query->orWhere(function ($slugQuery) use ($materialSlugs) {
                        $slugQuery->whereNull('material_id')
                            ->whereIn('material_slug', $materialSlugs);
                    });
                }
            })
            ->get();

        $progressByMaterial = [];
        foreach ($progressRows as $progress) {
            $key = $progress->material_id ? ('id:' . $progress->material_id) : ('slug:' . (string) $progress->material_slug);
            $progressByMaterial[$key] = $progress;
        }

        $assignmentByMaterial = [];
        foreach ($assignmentRows as $assignment) {
            $key = $assignment->material_id ? ('id:' . $assignment->material_id) : ('slug:' . (string) $assignment->material_slug);
            $assignmentByMaterial[$key] = $assignment;
        }

        $materialStates = [];
        $totalPercent = 0;
        $completedMaterials = 0;

        foreach ($materials as $material) {
            $key = $this->getMaterialProgressKey($material);
            $progressRecord = $progressByMaterial[$key] ?? null;
            $assignmentRecord = $assignmentByMaterial[$key] ?? null;

            $progressMetadata = is_array($progressRecord?->metadata ?? null) ? $progressRecord->metadata : [];

            $hasVideo = $this->materialHasVideoPart($material);
            $hasAssignment = $this->materialHasAssignmentPart($material);
            $isRead = ! empty($progressMetadata['material_read']) || $progressRecord?->status === 'completed';
            $isVideoWatched = ! $hasVideo || ! empty($progressMetadata['video_watched']);
            $isAssignmentSubmitted = ! $hasAssignment || ($assignmentRecord && ($assignmentRecord->submitted_at || $assignmentRecord->file_path));

            $partsTotal = $hasVideo ? 3 : 2;
            $partsCompleted = 0;
            if ($isRead) {
                $partsCompleted++;
            }
            if ($isVideoWatched && $hasVideo) {
                $partsCompleted++;
            }
            if ($isAssignmentSubmitted) {
                $partsCompleted++;
            }

            $completionPercentage = (int) round(($partsCompleted / $partsTotal) * 100);
            $isCompleted = $partsCompleted >= $partsTotal;

            if ($isCompleted) {
                $completedMaterials++;
            }

            $totalPercent += $completionPercentage;

            $materialStates[$key] = [
                'is_read' => $isRead,
                'is_video_watched' => $isVideoWatched,
                'is_assignment_submitted' => $isAssignmentSubmitted,
                'has_video' => $hasVideo,
                'has_assignment' => $hasAssignment,
                'parts_total' => $partsTotal,
                'parts_completed' => $partsCompleted,
                'completion_percentage' => $completionPercentage,
                'is_completed' => $isCompleted,
            ];
        }

        return [
            'completed_materials' => $completedMaterials,
            'total_materials' => $totalMaterials,
            'overall_percentage' => (int) round($totalPercent / $totalMaterials),
            'material_states' => $materialStates,
        ];
    }

    private function getMaterialProgressKey($material): string
    {
        if (isset($material->id) && $material->id) {
            return 'id:' . $material->id;
        }

        return 'slug:' . (string) ($material->slug ?? '');
    }

    private function materialHasVideoPart($material): bool
    {
        return trim((string) ($material->video_url ?? '')) !== '';
    }

    private function materialHasAssignmentPart($material): bool
    {
        if (($material->type ?? null) === 'assignment') {
            return true;
        }

        if (isset($material->assignment) && trim((string) $material->assignment) !== '') {
            return true;
        }

        $metadata = is_array($material->metadata ?? null) ? $material->metadata : [];

        return trim((string) ($metadata['assignment'] ?? '')) !== '';
    }

    private function resolveOrCreateMaterialProgress(Module $module, $material, User $user): ParticipantProgress
    {
        $query = [
            'user_id' => $user->id,
            'module_id' => $module->id,
        ];

        if (isset($material->id) && $material->id) {
            $query['material_id'] = $material->id;
        } else {
            $query['material_slug'] = (string) ($material->slug ?? '');
        }

        return ParticipantProgress::firstOrCreate(
            $query,
            [
                'status' => 'in_progress',
                'progress_percentage' => 0,
                'started_at' => now(),
            ]
        );
    }
}
