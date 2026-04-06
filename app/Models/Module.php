<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'duration',
        'cover',
        'status',
        'chapters',
        'participants_count',
    ];

    protected $casts = [
        'chapters' => 'array',
        'participants_count' => 'integer',
    ];

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover) {
            return null;
        }

        return asset('storage/' . ltrim($this->cover, '/'));
    }
}
