<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'video_url',
        'order',
        'type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'order' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        return asset('storage/' . ltrim($this->thumbnail, '/'));
    }

    public function getVideoUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // If it's a full URL, return as is
        if (filter_var($this->video_url, FILTER_VALIDATE_URL)) {
            return $this->video_url;
        }

        // Otherwise, assume it's a local file
        return asset('storage/' . ltrim($this->video_url, '/'));
    }
}
