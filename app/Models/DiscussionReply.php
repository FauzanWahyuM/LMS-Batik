<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionReply extends Model
{
    use HasFactory;

    protected $table = 'forum_discussion_replies';

    protected $fillable = [
        'discussion_id',
        'parent_id',
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function discussion()
    {
        return $this->belongsTo(ForumDiscussion::class, 'discussion_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
