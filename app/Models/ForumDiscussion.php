<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumDiscussion extends Model
{
    use HasFactory;

    protected $table = 'forum_discussions';

    protected $fillable = [
        'module_id',
        'theme',
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'title',
        'content',
        'is_pinned',
        'is_closed',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_closed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_id')
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->with('childrenRecursive');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function getThemeAttribute(): string
    {
        return (string) ($this->attributes['theme'] ?? ($this->attributes['title'] ?? ''));
    }

    public function setThemeAttribute($value): void
    {
        $this->attributes['theme'] = $value;
    }
}
