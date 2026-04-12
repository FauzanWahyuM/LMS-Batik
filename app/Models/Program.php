<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration_hours',
        'fee_amount',
        'fee_unit',
        'description',
        'benefits',
        'is_active',
    ];

    protected $casts = [
        'duration_hours' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];
}
