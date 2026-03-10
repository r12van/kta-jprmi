<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengurusStruktural extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_id', 'tingkat', 'jabatan', 'nama_bidang', 'periode_awal', 'periode_akhir', 'is_active'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
