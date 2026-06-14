<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationGroup extends Model
{
    protected $table = 'registration_groups';

    protected $fillable = [
        'nama_lembaga',
        'alamat_pic',
        'email_pic',
        'no_handphone_pic',
        'nama_pic',
        'jumlah_peserta',
        'surat_resmi',
        'program_id',
        'status',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
