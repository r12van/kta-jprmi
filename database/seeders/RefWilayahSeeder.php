<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefWilayahSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. Data 38 Provinsi di Indonesia (Kode Kemendagri/BPS)
        $provinsi = [
            ['kode_prov' => '11', 'nama_provinsi' => 'Aceh'],
            ['kode_prov' => '12', 'nama_provinsi' => 'Sumatera Utara'],
            ['kode_prov' => '13', 'nama_provinsi' => 'Sumatera Barat'],
            ['kode_prov' => '14', 'nama_provinsi' => 'Riau'],
            ['kode_prov' => '15', 'nama_provinsi' => 'Jambi'],
            ['kode_prov' => '16', 'nama_provinsi' => 'Sumatera Selatan'],
            ['kode_prov' => '17', 'nama_provinsi' => 'Bengkulu'],
            ['kode_prov' => '18', 'nama_provinsi' => 'Lampung'],
            ['kode_prov' => '19', 'nama_provinsi' => 'Kepulauan Bangka Belitung'],
            ['kode_prov' => '21', 'nama_provinsi' => 'Kepulauan Riau'],
            ['kode_prov' => '31', 'nama_provinsi' => 'DKI Jakarta'],
            ['kode_prov' => '32', 'nama_provinsi' => 'Jawa Barat'],
            ['kode_prov' => '33', 'nama_provinsi' => 'Jawa Tengah'],
            ['kode_prov' => '34', 'nama_provinsi' => 'DI Yogyakarta'],
            ['kode_prov' => '35', 'nama_provinsi' => 'Jawa Timur'],
            ['kode_prov' => '36', 'nama_provinsi' => 'Banten'],
            ['kode_prov' => '51', 'nama_provinsi' => 'Bali'],
            ['kode_prov' => '52', 'nama_provinsi' => 'Nusa Tenggara Barat'],
            ['kode_prov' => '53', 'nama_provinsi' => 'Nusa Tenggara Timur'],
            ['kode_prov' => '61', 'nama_provinsi' => 'Kalimantan Barat'],
            ['kode_prov' => '62', 'nama_provinsi' => 'Kalimantan Tengah'],
            ['kode_prov' => '63', 'nama_provinsi' => 'Kalimantan Selatan'],
            ['kode_prov' => '64', 'nama_provinsi' => 'Kalimantan Timur'],
            ['kode_prov' => '65', 'nama_provinsi' => 'Kalimantan Utara'],
            ['kode_prov' => '71', 'nama_provinsi' => 'Sulawesi Utara'],
            ['kode_prov' => '72', 'nama_provinsi' => 'Sulawesi Tengah'],
            ['kode_prov' => '73', 'nama_provinsi' => 'Sulawesi Selatan'],
            ['kode_prov' => '74', 'nama_provinsi' => 'Sulawesi Tenggara'],
            ['kode_prov' => '75', 'nama_provinsi' => 'Gorontalo'],
            ['kode_prov' => '76', 'nama_provinsi' => 'Sulawesi Barat'],
            ['kode_prov' => '81', 'nama_provinsi' => 'Maluku'],
            ['kode_prov' => '82', 'nama_provinsi' => 'Maluku Utara'],
            ['kode_prov' => '91', 'nama_provinsi' => 'Papua'],
            ['kode_prov' => '92', 'nama_provinsi' => 'Papua Barat'],
            ['kode_prov' => '93', 'nama_provinsi' => 'Papua Selatan'],
            ['kode_prov' => '94', 'nama_provinsi' => 'Papua Tengah'],
            ['kode_prov' => '95', 'nama_provinsi' => 'Papua Pegunungan'],
            ['kode_prov' => '96', 'nama_provinsi' => 'Papua Barat Daya'],
        ];

        // Insert Provinsi
        foreach ($provinsi as $p) {
            DB::table('ref_provinsi')->insert([
                'kode_prov' => $p['kode_prov'],
                'nama_provinsi' => $p['nama_provinsi'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // 2. Data Beberapa Kabupaten/Kota (Sebagai Sampel Awal)
        // Kita ambil ID Provinsi dari database yang baru saja diinsert
        $provDKI = DB::table('ref_provinsi')->where('kode_prov', '31')->first()->id;
        $provJabar = DB::table('ref_provinsi')->where('kode_prov', '32')->first()->id;
        $provKalbar = DB::table('ref_provinsi')->where('kode_prov', '61')->first()->id;
        $provAceh = DB::table('ref_provinsi')->where('kode_prov', '11')->first()->id;

        $kota = [
            // DKI Jakarta
            ['provinsi_id' => $provDKI, 'kode_kota' => '71', 'nama_kota' => 'Jakarta Pusat'],
            ['provinsi_id' => $provDKI, 'kode_kota' => '72', 'nama_kota' => 'Jakarta Utara'],
            ['provinsi_id' => $provDKI, 'kode_kota' => '73', 'nama_kota' => 'Jakarta Barat'],
            ['provinsi_id' => $provDKI, 'kode_kota' => '74', 'nama_kota' => 'Jakarta Selatan'],
            ['provinsi_id' => $provDKI, 'kode_kota' => '75', 'nama_kota' => 'Jakarta Timur'],

            // Jawa Barat
            ['provinsi_id' => $provJabar, 'kode_kota' => '73', 'nama_kota' => 'Kota Bandung'],
            ['provinsi_id' => $provJabar, 'kode_kota' => '71', 'nama_kota' => 'Kota Bogor'],
            ['provinsi_id' => $provJabar, 'kode_kota' => '75', 'nama_kota' => 'Kota Bekasi'],
            ['provinsi_id' => $provJabar, 'kode_kota' => '76', 'nama_kota' => 'Kota Depok'],
            ['provinsi_id' => $provJabar, 'kode_kota' => '01', 'nama_kota' => 'Kabupaten Bogor'],

            // Kalimantan Barat
            ['provinsi_id' => $provKalbar, 'kode_kota' => '71', 'nama_kota' => 'Kota Pontianak'],
            ['provinsi_id' => $provKalbar, 'kode_kota' => '72', 'nama_kota' => 'Kota Singkawang'],
            ['provinsi_id' => $provKalbar, 'kode_kota' => '01', 'nama_kota' => 'Kabupaten Sambas'],

            // Aceh
            ['provinsi_id' => $provAceh, 'kode_kota' => '71', 'nama_kota' => 'Kota Banda Aceh'],
            ['provinsi_id' => $provAceh, 'kode_kota' => '73', 'nama_kota' => 'Kota Lhokseumawe'],
        ];

        foreach ($kota as $k) {
            DB::table('ref_kabupaten_kota')->insert([
                'provinsi_id' => $k['provinsi_id'],
                'kode_kota' => $k['kode_kota'],
                'nama_kota' => $k['nama_kota'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        /* * Catatan: Untuk 514 Kota Kabupaten lengkap, disarankan menggunakan
         * package seperti "laravolt/indonesia" agar tidak perlu mengetik manual
         * ratusan baris data ke dalam seeder.
         */
    }
}
