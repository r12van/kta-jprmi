<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\AnggotaDraft;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. BUAT BASE QUERY BERDASARKAN ROLE
        $baseQuery = Anggota::query();
        $draftQuery = AnggotaDraft::where('status_approval', 'Menunggu');

        if ($user->role == 'Admin PW') {
            $baseQuery->where('provinsi_id', $user->provinsi_id);
            $draftQuery->whereHas('anggota', function($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        } elseif ($user->role == 'Admin PD') {
            $baseQuery->where('kota_id', $user->kota_id);
            $draftQuery->whereHas('anggota', function($q) use ($user) {
                $q->where('kota_id', $user->kota_id);
            });
        }

        // 2. HITUNG METRIK UTAMA
        $totalVerified = (clone $baseQuery)->where('status_verifikasi', 'Verified')->count();
        $totalPending  = (clone $baseQuery)->where('status_verifikasi', 'Pending')->count();
        $totalDrafts   = $draftQuery->count();

        // 3. KOMPOSISI ANGGOTA
        $totalPengurus = (clone $baseQuery)->where('status_verifikasi', 'Verified')->where('jenis_anggota', 'Pengurus')->count();
        $totalRemaja   = (clone $baseQuery)->where('status_verifikasi', 'Verified')->where('jenis_anggota', 'Remaja Mesjid')->count();
        $totalAlumni   = (clone $baseQuery)->where('status_verifikasi', 'Verified')->where('jenis_anggota', 'Alumni')->count();

        // 4. GRAFIK PERTUMBUHAN TAHUNAN
        $chartData = (clone $baseQuery)->where('status_verifikasi', 'Verified')
            ->select('tahun_masuk', DB::raw('count(*) as total'))
            ->groupBy('tahun_masuk')->orderBy('tahun_masuk', 'ASC')->get();
        $labelsTahun = $chartData->pluck('tahun_masuk')->toArray();
        $dataPertumbuhan = $chartData->pluck('total')->toArray();

        // 5. GENDER (LAKI-LAKI & PEREMPUAN)
        $genderData = (clone $baseQuery)->where('status_verifikasi', 'Verified')
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')->pluck('total', 'jenis_kelamin')->toArray();

        $totalLaki = $genderData['L'] ?? 0;
        $totalPerempuan = $genderData['P'] ?? 0;

        // 6. SEBARAN UMUR ANGGOTA
        $birthDates = (clone $baseQuery)->where('status_verifikasi', 'Verified')
            ->whereNotNull('tanggal_lahir')->pluck('tanggal_lahir');

        $ageBrackets = [
            '< 20 Tahun' => 0,
            '21 - 30 Tahun' => 0,
            '31 - 40 Tahun' => 0,
            '41 - 50 Tahun' => 0,
            '> 50 Tahun' => 0
        ];

        foreach($birthDates as $date) {
            $age = Carbon::parse($date)->age;
            if($age <= 20) $ageBrackets['< 20 Tahun']++;
            elseif($age <= 30) $ageBrackets['21 - 30 Tahun']++;
            elseif($age <= 40) $ageBrackets['31 - 40 Tahun']++;
            elseif($age <= 50) $ageBrackets['41 - 50 Tahun']++;
            else $ageBrackets['> 50 Tahun']++;
        }

        // 7. SEBARAN PROVINSI
        $tabelProvinsi = (new Province)->getTable();
        $provinceData = (clone $baseQuery)->where('status_verifikasi', 'Verified')
            ->join($tabelProvinsi, 'anggota.provinsi_id', '=', $tabelProvinsi.'.id')
            ->select($tabelProvinsi.'.name as provinsi', DB::raw('count(*) as total'))
            ->groupBy('anggota.provinsi_id', $tabelProvinsi.'.name')
            ->orderByDesc('total')
            ->limit(10) // Tampilkan Top 10 Provinsi Terbanyak
            ->get();

        $labelsProvinsi = $provinceData->pluck('provinsi')->toArray();
        $dataProvinsi = $provinceData->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalVerified', 'totalPending', 'totalDrafts',
            'totalPengurus', 'totalRemaja', 'totalAlumni',
            'labelsTahun', 'dataPertumbuhan',
            'totalLaki', 'totalPerempuan',
            'ageBrackets',
            'labelsProvinsi', 'dataProvinsi'
        ));
    }
}
