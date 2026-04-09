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
        $completedMaterialIds = ParticipantProgress::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('status', 'completed')
            ->whereNotNull('material_id')
            ->pluck('material_id')
            ->all();

        $completedCount = $materials->filter(function ($material) use ($completedMaterialIds) {
            return isset($material->id) && in_array($material->id, $completedMaterialIds, true);
        })->count();
        $totalCount = $materials->count();
        $overallProgress = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;

        $materials = $materials->map(function ($material) use ($completedMaterialIds) {
            $material->is_completed = isset($material->id) && in_array($material->id, $completedMaterialIds, true);
            return $material;
        });

        return [
            'module' => $module,
            'materials' => $materials,
            'progress' => $progress,
            'assignments' => $assignments,
            'task_instructions' => $taskInstructions,
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
            'overall_progress' => $overallProgress,
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
        if ($materials->isEmpty()) {
            return 0;
        }

        $materialIds = $materials->pluck('id')->filter()->values();
        if ($materialIds->isEmpty()) {
            return 0;
        }

        $completedMaterials = ParticipantProgress::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->whereIn('material_id', $materialIds)
            ->where('status', 'completed')
            ->count();

        return (int) (($completedMaterials / $materials->count()) * 100);
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
    public function markMaterialAsCompleted(Module $module, ModuleMaterial $material, User $user): ParticipantProgress
    {
        $progress = ParticipantProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'material_id' => $material->id,
            ],
            [
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]
        );

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

        $existing = ParticipantAssignment::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('material_id', $material?->id)
            ->first();

        if ($existing && $existing->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        return ParticipantAssignment::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'material_id' => $material?->id,
            ],
            [
                'material_slug' => $materialSlug ?: $material?->slug,
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
        $materials = $module->materials;
        $completedMaterials = 0;
        $totalAssignments = 0;
        $gradedAssignments = 0;

        foreach ($materials as $material) {
            $progress = ParticipantProgress::where('user_id', $user->id)
                ->where('module_id', $module->id)
                ->where('material_id', $material->id)
                ->where('status', 'completed')
                ->first();

            if ($progress) {
                $completedMaterials++;
            }

            if ($material->type === 'assignment') {
                $totalAssignments++;
                $assignment = ParticipantAssignment::where('user_id', $user->id)
                    ->where('module_id', $module->id)
                    ->where('material_id', $material->id)
                    ->first();

                if ($assignment && $assignment->isGraded()) {
                    $gradedAssignments++;
                }
            }
        }

        return [
            'total_materials' => $materials->count(),
            'completed_materials' => $completedMaterials,
            'completion_percentage' => $materials->count() > 0 ? (int) (($completedMaterials / $materials->count()) * 100) : 0,
            'total_assignments' => $totalAssignments,
            'graded_assignments' => $gradedAssignments,
        ];
    }
}
