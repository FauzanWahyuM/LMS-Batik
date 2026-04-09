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

    public function getContentAttribute($value): ?string
    {
        return normalize_uploaded_content_html($value);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnail = (string) ($this->getAttribute('thumbnail') ?? '');

        if ($thumbnail === '') {
            return null;
        }

        return route('public-file', ['path' => ltrim($thumbnail, '/')]);
    }

    public function getVideoUrlAttribute($value): ?string
    {
        $videoUrl = (string) ($value ?? '');

        if ($videoUrl === '') {
            return null;
        }

        // If it's a full URL, return as is
        if (filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            return $videoUrl;
        }

        return route('public-file', ['path' => ltrim($videoUrl, '/')]);
    }
}
