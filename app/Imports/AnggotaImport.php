<?php

namespace App\Imports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Log;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Carbon\Carbon;

class AnggotaImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    public function model(array $row)
    {
        // 1. FILTER BARIS HANTU (Excel Empty Rows)
        // Jika nama kosong, lompati baris ini tanpa mencatat error
        if (empty(trim($row['nama'] ?? ''))) {
            return null;
        }

        try {
            // 2. TANGANI NIK KOSONG & DUPLIKAT
            $nik = trim($row['nik'] ?? '');

            // Jika NIK benar-benar kosong atau berisi strip '-', kita buatkan NIK Dummy sementara
            if (empty($nik) || $nik == '-') {
                // Format Dummy NIK agar tetap unik di database
                $nik = '';
            } else {
                // Cek apakah NIK sudah pernah masuk ke DB
                $existing = Anggota::where('nik', $nik)->first();
                if ($existing) {
                    Log::info("Import Skip: NIK {$nik} sudah ada di database (Nama: {$row['nama']}).");
                    return null;
                }
            }

            // 3. PENCARIAN WILAYAH LEBIH FLEKSIBEL
            $namaProvinsi = trim($row['provinsi'] ?? '');
            // Menggunakan str_replace untuk membersihkan kata "Kepulauan" atau lainnya jika perlu
            $provinsi = Province::where('name', 'LIKE', '%' . $namaProvinsi . '%')->first();

            $kota = null;
            if ($provinsi) {
                // Bersihkan string kota di CSV agar pencarian lebih akurat
                $namaKota = str_replace(['Kota ', 'Kabupaten ', 'Kab '], '', trim($row['kotakabupaten'] ?? ''));
                $kota = City::where('province_code', $provinsi->code)
                            ->where('name', 'LIKE', '%' . $namaKota . '%')
                            ->first();
            }

            // Jika GAGAL menemukan kota, berikan Fallback (Default ke Ibukota Provinsi atau ID 1)
            // Tujuannya agar data orangnya TETAP MASUK ke sistem, wilayahnya bisa di-edit belakangan.
            if (!$provinsi) {
                // Default ke DKI Jakarta jika Provinsi benar-benar hancur penulisannya
                $provinsi = Province::where('code', '31')->first();
            }
            if (!$kota) {
                // Ambil sembarang kota di provinsi tersebut sebagai penampung sementara
                $kota = City::where('province_code', $provinsi->code)->first();
            }

            // 4. LOGIC GENERATE NIA (PP.KK.YY.UUUUUU)
            $pp = $provinsi->code;
            $kk = substr($kota->code, 2, 2);

            $tahun_masuk = trim($row['tahun_bergabung'] ?? date('Y'));
            // Pastikan tahun masuk 4 digit, jika tidak pakai tahun sekarang
            if(strlen($tahun_masuk) != 4) $tahun_masuk = date('Y');
            $yy = substr($tahun_masuk, -2);

            $no_urut_raw = isset($row['no_urut_6_digit']) && $row['no_urut_6_digit'] != ''
                            ? $row['no_urut_6_digit']
                            : rand(1000, 9999); // Fallback jika tidak ada no_urut

            $uuuuuu = str_pad((int)$no_urut_raw, 6, '0', STR_PAD_LEFT);
            $nia_final = "{$pp}.{$kk}.{$yy}.{$uuuuuu}";


            // 5. PARSING TANGGAL AMAN
            $tgl_lahir = null;
            if (!empty($row['tanggal_lahir'])) {
                $str_date = str_replace('/', '-', trim($row['tanggal_lahir']));
                try {
                    $tgl_lahir = Carbon::parse($str_date)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tgl_lahir = null;
                }
            }
            $tanggal_lahir_formatted = $tgl_lahir ? Carbon::parse($tgl_lahir)->format('dmy') : '000000';
            if ($nik == '') {
                $nik = $pp.$kk.'00'.$tanggal_lahir_formatted.'0000'; // Format NIK Dummy: ProvinsiID + KotaID + '00' + TanggalLahir(YYYYMMDD) + '0000'
            }

            // 6. SIMPAN KE DATABASE
            return new Anggota([
                'nia'               => $nia_final,
                'nik'               => $nik,
                'jenis_anggota'     => 'Remaja Mesjid', // Set default

                'nama_lengkap'      => trim($row['nama']),
                'tempat_lahir'      => trim($row['tempat_lahir'] ?? ''),
                'tanggal_lahir'     => $tgl_lahir,
                'jenis_kelamin'     => strtoupper(trim($row['jenis_kelamin'] ?? 'L')) == 'P' ? 'P' : 'L',
                'no_hp'             => trim($row['no_hp'] ?? ''),

                'provinsi_id'       => $provinsi->id,
                'kota_id'           => $kota->id,
                'alamat_lengkap'    => trim($row['alamat'] ?? ''),

                'tahun_masuk'       => $tahun_masuk,
                'foto_diri'         => 'default-avatar.png',

                'status_verifikasi' => 'Verified',
                'created_by'        => auth()->user()?->id ?? 1,
            ]);

        } catch (\Exception $e) {
            // Ini sangat penting! Mencatat bari mana yang error dan apa penyebabnya
            Log::error("Import Error pada baris Nama {$row['nama']}: " . $e->getMessage());
            return null;
        }
    }
}
