<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function index()
    {
        // Hitung total anggota yang sudah Verified di setiap provinsi
        $sebaran = DB::table('anggota')
            ->join('indonesia_provinces', 'anggota.provinsi_id', '=', 'indonesia_provinces.id')
            ->select('indonesia_provinces.name as provinsi', DB::raw('count(anggota.id) as total'))
            ->where('anggota.status_verifikasi', 'Verified')
            ->groupBy('anggota.provinsi_id', 'indonesia_provinces.name')
            ->get();

        // Hitung Total Seluruh Anggota Nasional
        $totalNasional = $sebaran->sum('total');

        return view('welcome', compact('sebaran', 'totalNasional'));
    }
}
