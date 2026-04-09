<?php

namespace App\Services;

use App\Models\ForumDiscussion;
use App\Models\Module;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ForumDiscussionService
{
    public function getForumModules(): EloquentCollection
    {
        return Module::query()
            ->orderBy('title')
            ->get(['slug', 'title']);
    }

    public function getForumDiscussions(?string $moduleSlug = null): EloquentCollection
    {
        $query = ForumDiscussion::with([
            'module',
            'replies' => function ($query): void {
                $query->orderBy('created_at');
            },
        ])->orderByDesc('is_pinned')->orderByDesc('created_at');

        if (!empty($moduleSlug)) {
            $query->where('module_id', $moduleSlug);
        }

        return $query->get();
    }

    public function getModuleDiscussions(string $moduleSlug): EloquentCollection
    {
        return $this->getForumDiscussions($moduleSlug);
    }
}