<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $guarded = ['id'];

    // --- RELASI DATABASE ---
    public function provinsi()
    {
        return $this->belongsTo(Province::class, 'provinsi_id');
    }

    public function kota()
    {
        return $this->belongsTo(City::class, 'kota_id');
    }

    public function drafts()
    {
        return $this->hasMany(AnggotaDraft::class, 'anggota_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- LOGIKA GENERATE NIA ---
    public function generateNia()
    {
        // Jika sudah punya NIA, jangan di-generate ulang
        if ($this->nia) {
            return $this->nia;
        }

        DB::transaction(function () {
            // 1. Ambil Kode Provinsi (PP), Kota (KK), dan Tahun (YY)
            $pp = $this->provinsi->code;
            $kk = substr($this->kota->code, 2, 2);

            $tahun_masuk = $this->tahun_masuk ?? date('Y');
            $yy = substr($tahun_masuk, -2);

            // 2. LOGIK BARU ANTI-BENTROK: Cari Nilai Tertinggi Nasional
            // Memecah string PP.KK.YY.UUUUUU, mengambil bagian belakang (UUUUUU), dan mencari nilai MAX-nya
            $maxSequence = DB::table('anggota')
                ->whereNotNull('nia')
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(nia, '.', -1) AS UNSIGNED)) as max_urut")
                ->value('max_urut');

            // 3. Tentukan Nomor Urut Berikutnya
            if ($maxSequence) {
                // Jika nilai tertinggi di CSV Anda adalah 1500, maka ini akan jadi 1501
                // Jika tertinggi 2005, maka jadi 2006. Tidak peduli urutan importnya.
                $nextSequence = $maxSequence + 1;
            } else {
                // Jika database benar-benar kosong
                $nextSequence = 2001;
            }

            // 4. Format kembali menjadi 6 Digit (Contoh: 1501 -> 001501)
            $uuuuuu = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);

            // 5. Gabungkan dan Simpan
            $this->nia = "{$pp}.{$kk}.{$yy}.{$uuuuuu}";
            $this->status_verifikasi = 'Verified';
            $this->save();
        });

        return $this->nia;
    }

    // --- LOGIKA UPDATE NIA JIKA DOMISILI BERUBAH ---
    public function syncNiaDenganDomisili()
    {
        // Jika belum punya NIA sama sekali, abaikan
        if (!$this->nia) {
            return;
        }
        $this->load(['provinsi', 'kota']);

        // 2. Ambil Kode Provinsi (PP) dan Kota (KK) terbaru
        $pp = $this->provinsi->code;
        $kk = substr($this->kota->code, 2, 2);
        $yy = substr($this->tahun_masuk, -2);

        // 3. Pecah NIA lama (Contoh: 31.71.24.002001)
        $parts = explode('.', $this->nia);

        if (count($parts) == 4) {
            $uuuuuu = end($parts); // Mengambil 6 digit terakhir ("002001")

            // Gabungkan menjadi NIA Baru
            $nia_baru = "{$pp}.{$kk}.{$yy}.{$uuuuuu}";

            // Simpan perubahan jika memang ada perbedaan
            if ($this->nia !== $nia_baru) {
                $this->nia = $nia_baru;
                $this->save(); // Simpan NIA baru ke database
            }
        }
    }

    public function kepengurusan()
    {
        return $this->hasMany(PengurusStruktural::class)->orderBy('periode_akhir', 'desc');
    }

    public function kepengurusanAktif()
    {
        return $this->hasOne(PengurusStruktural::class)
            ->where('is_active', true)
            ->latestOfMany('id');
    }
}
