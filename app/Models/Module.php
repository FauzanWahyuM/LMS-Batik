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

    public function getProgressForUser(User $user): ?ParticipantProgress
    {
        return $this->progress()->where('user_id', $user->id)->first();
    }

    public function getAssignmentsForUser(User $user)
    {
        return $this->assignments()->where('user_id', $user->id)->get();
    }
}
