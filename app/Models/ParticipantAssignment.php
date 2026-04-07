<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'material_id',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'score',
        'feedback',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(ModuleMaterial::class, 'material_id');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isGraded(): bool
    {
        return !is_null($this->graded_at);
    }
}
