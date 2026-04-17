<?php

namespace App\Services;

use App\Models\ForumDiscussion;
use App\Models\Discussion;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ForumDiscussionService
{
    /**
     * @return array<int, array<string, string>>
     */
    public function getForumThemes(): array
    {
        if (!Schema::hasTable('forum_discussions')) {
            return [];
        }

        $themeKeys = ForumDiscussion::query()
            ->whereNotNull('theme')
            ->where('theme', '!=', '')
            ->orderBy('theme')
            ->pluck('theme')
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn (string $item) => $item !== '')
            ->unique()
            ->values();

        if ($themeKeys->isEmpty()) {
            return [];
        }

        return $themeKeys->map(function (string $key): array {
            return [
                'key' => $key,
                'label' => $key,
            ];
        })->all();
    }

    public function getForumModules(): EloquentCollection
    {
        return Module::query()
            ->orderBy('title')
            ->get(['slug', 'title']);
    }

    public function getForumDiscussions(?string $theme = null): EloquentCollection
    {
        $query = ForumDiscussion::with([
            'module',
            'replies' => function ($query): void {
                $query->orderBy('created_at');
            },
        ])->orderByDesc('is_pinned')->orderByDesc('created_at');

        if (!empty($theme)) {
            $query->where('theme', $theme);
        }

        return $query->get();
    }

    public function getModuleDiscussions(string $moduleSlug): EloquentCollection
    {
        $moduleId = Module::query()->where('slug', $moduleSlug)->value('id');

        if (!$moduleId) {
            return new EloquentCollection();
        }

        return Discussion::with([
            'module',
            'replies' => function ($query): void {
                $query->orderBy('created_at');
            },
        ])
            ->where('module_id', $moduleId)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();
    }
}
