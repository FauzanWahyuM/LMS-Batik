<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleDiscussionReply extends Model
{
    use HasFactory;

    protected $table = 'module_discussion_replies';

    protected $fillable = [
        'discussion_id',
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

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }
}
