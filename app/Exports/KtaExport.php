<?php

namespace App\Exports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KtaExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $provinsi_id;
    protected $kota_id;

    public function __construct($provinsi_id = null, $kota_id = null)
    {
        $this->provinsi_id = $provinsi_id;
        $this->kota_id = $kota_id;
    }

    public function query()
    {
        $query = Anggota::query()->where('status_verifikasi', 'Verified')->orderBy('nia', 'ASC');

        if ($this->provinsi_id) {
            $query->where('provinsi_id', $this->provinsi_id);
        }
        if ($this->kota_id) {
            $query->where('kota_id', $this->kota_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['NIA (Nomor Induk Anggota)', 'Nama Lengkap', 'Alamat Lengkap'];
    }

    public function map($anggota): array
    {
        return [
            $anggota->nia,
            $anggota->nama_lengkap,
            $anggota->alamat_lengkap
        ];
    }
}
