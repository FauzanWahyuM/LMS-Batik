<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'duration' => 'float',
        'participants_count' => 'integer',
    ];

    public function materials()
    {
        return $this->hasMany(ModuleMaterial::class)->orderBy('order');
    }

    public function assignments()
    {
        return $this->hasMany(ParticipantAssignment::class);
    }

    public function progress()
    {
        return $this->hasMany(ParticipantProgress::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class, 'module_id')->orderByDesc('created_at');
    }

    public function getProgressForUser(User $user): ?ParticipantProgress
    {
        return $this->progress()->where('user_id', $user->id)->first();
    }

    public function getAssignmentsForUser(User $user)
    {
        return $this->assignments()->where('user_id', $user->id)->get();
    }
}
