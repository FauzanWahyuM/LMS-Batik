<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    use HasFactory;

    protected $table = 'discussions';

    protected $fillable = [
        'module_id',
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

    public function module()
    : BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ModuleDiscussionReply::class, 'discussion_id')->orderBy('created_at');
    }
}
