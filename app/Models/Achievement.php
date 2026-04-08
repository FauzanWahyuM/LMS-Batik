<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'event_name',
        'winner_name',
        'description',
        'rank',
        'year',
        'is_active',
    ];

    protected $casts = [
        'rank' => 'integer',
        'year' => 'integer',
        'is_active' => 'boolean',
    ];
}
