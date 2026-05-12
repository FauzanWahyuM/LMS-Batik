<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'stock',
        'minimum_stock',
        'description',
        'image_path',
    ];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];
}
