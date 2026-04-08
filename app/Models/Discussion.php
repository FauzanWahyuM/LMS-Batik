<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discussion extends Model
{
    use HasFactory;

    protected $table = 'discussions';

    protected $fillable = [
        'module_id',
        'user_id',
        'user_name',
        'user_email',
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
    {
        return $this->belongsTo(Module::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class);
    }
}
