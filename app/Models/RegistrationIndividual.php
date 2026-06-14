<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationIndividual extends Model
{
    protected $table = 'registration_individuals';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_handphone',
        'alamat',
        'pendidikan_terakhir',
        'motivasi',
        'program_id',
        'status',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
