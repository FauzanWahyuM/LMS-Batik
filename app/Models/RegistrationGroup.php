<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationGroup extends Model
{
    protected $fillable = [
        'nama_lembaga',
        'alamat_pic',
        'email_pic',
        'no_handphone_pic',
        'nama_pic',
        'jumlah_peserta',
        'surat_resmi',
        'status',
    ];
}
