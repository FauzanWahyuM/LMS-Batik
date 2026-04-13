<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status',
    ];
}
