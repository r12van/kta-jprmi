<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaDraft extends Model
{
    protected $table = 'anggota_drafts';
    protected $guarded = ['id'];

    // Relasi kembali ke Anggota Asli
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    // Relasi siapa admin yang mengajukan edit (Admin PD)
    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    // Fungsi helper untuk melakukan Approve data
    public function approveDraft($reviewer_id)
    {
        $anggotaAsli = $this->anggota;

        // Timpa data lama dengan data baru yang diajukan, jika ada
        $anggotaAsli->update([
            'nama_lengkap'      => $this->nama_lengkap_baru ?? $anggotaAsli->nama_lengkap,
            'jenis_anggota'     => $this->jenis_anggota_baru ?? $anggotaAsli->jenis_anggota,
            'alamat_lengkap'    => $this->alamat_lengkap_baru ?? $anggotaAsli->alamat_lengkap,
            'foto_diri'         => $this->foto_diri_baru ?? $anggotaAsli->foto_diri,
            'status_verifikasi' => 'Verified' // Kembalikan statusnya jadi verified
        ]);

        // Tandai draft ini sudah disetujui
        $this->update([
            'status_approval' => 'Disetujui',
            'reviewed_by'     => $reviewer_id
        ]);
    }
}
