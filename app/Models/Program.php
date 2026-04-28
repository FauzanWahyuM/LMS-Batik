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
        'duration_unit',
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

    public function getDurationLabelAttribute(): string
    {
        $value = (float) $this->duration_hours;
        $hasFraction = abs($value - floor($value)) > 0.00001;
        $decimals = $hasFraction ? 1 : 0;
        $unit = $this->duration_unit === 'minutes' ? 'menit' : 'jam';

        return number_format($value, $decimals, ',', '.') . ' ' . $unit;
    }
}
