<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'fee_amount',
        'fee_unit',
        'description',
        'benefits',
        'training_schedules',
        'is_active',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'benefits' => 'array',
        'training_schedules' => 'array',
        'is_active' => 'boolean',
    ];

    public function getDurationLabelAttribute(): string
    {
        return $this->duration ?? '-';
    }
}
