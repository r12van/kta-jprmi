<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Imports\AnggotaImport;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use App\Models\AnggotaDraft;
use App\Models\PengurusStruktural;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\KtaExport;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AnggotaController extends Controller
{
    public function index()
    {
        // Hanya kirim daftar provinsi ke view untuk filter (Khusus Admin PP)
        $provinces = Cache::remember('wilayah:provinces:all', now()->addHours(24), function () {
            return Province::select('id', 'name', 'code')
                ->orderBy('name')
                ->get();
        });
        return view('admin.anggota.index', compact('provinces'));
    }
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Mulai query dengan memanggil relasi
    //     $query = Anggota::with(['provinsi', 'kota']);

    //     // Filter Hak Akses
    //     if ($user->role == 'Admin PW') {
    //         $query->where('provinsi_id', $user->provinsi_id);
    //     } elseif ($user->role == 'Admin PD') {
    //         $query->where('kota_id', $user->kota_id);
    //     }

    //     // KARENA KITA PAKAI DATATABLES CLIENT-SIDE:
    //     // Kita tidak pakai paginate(15) lagi, melainkan get() agar semua data diload
    //     // dan diserahkan ke DataTables untuk diatur halamannya.

    //     // Trik SQL: Mengambil 6 digit terakhir setelah titik terakhir untuk disorting
    //     // ISNULL(nia) agar yang belum punya NIA (masih pending) ditaruh di paling bawah
    //     $query->orderByRaw("ISNULL(nia), CAST(SUBSTRING_INDEX(nia, '.', -1) AS UNSIGNED) ASC");

    //     $anggotas = $query->get();

    //     return view('admin.anggota.index', compact('anggotas'));
    // }
    // --- 2. ENDPOINT SERVER-SIDE DATATABLES ---
    public function data(Request $request)
    {
        $user = Auth::user();

        // Optimasi: hanya load relasi yang dipakai + angka urut NIA untuk sorting cepat
        $query = Anggota::query()
            ->with([
                'provinsi:id,name',
                'kota:id,name',
                'kepengurusanAktif' => function ($q) {
                    $q->select(
                        'pengurus_strukturals.id',
                        'pengurus_strukturals.anggota_id',
                        'pengurus_strukturals.jabatan',
                        'pengurus_strukturals.is_active'
                    );
                },
            ])
            ->select('anggota.*')
            ->selectRaw("CAST(SUBSTRING_INDEX(anggota.nia, '.', -1) AS UNSIGNED) as nia_urut");

        // Proteksi Data Berdasarkan Role
        if ($user->role == 'Admin PW') {
            $query->where('provinsi_id', $user->provinsi_id);
        } elseif ($user->role == 'Admin PD') {
            $query->where('kota_id', $user->kota_id);
        } else {
            // Filter Khusus Admin PP
            if ($request->filled('provinsi_id')) {
                $query->where('provinsi_id', $request->provinsi_id);
            }
        }

        // Filter Tambahan (Berlaku untuk semua Role)
        if ($request->filled('jenis_anggota')) {
            $query->where('jenis_anggota', $request->jenis_anggota);
        }
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        return DataTables::of($query)
            ->addColumn('no_urut', function ($row) {
                if ($row->status_verifikasi === 'Pending' || ! $row->nia_urut) {
                    return '<span class="text-muted fst-italic small">--</span>';
                }

                return '<span class="fw-bold text-primary">'.str_pad((string) $row->nia_urut, 6, '0', STR_PAD_LEFT).'</span>';
            })
            ->editColumn('nia', function($row) {
                if($row->status_verifikasi == 'Pending') return '<span class="text-muted fst-italic small">-- Belum Ada --</span>';
                return '<span class="fw-bold text-dark">'.$row->nia.'</span>';
            })
            ->editColumn('nama_lengkap', function($row) {
                return '<div class="fw-bold text-dark">'.$row->nama_lengkap.'</div>
                        <small class="text-muted">NIK: '.$row->nik.'</small>';
            })
            ->addColumn('wilayah', function($row) {
                return '<div class="fw-bold text-dark">'.($row->provinsi->name ?? '-').'</div>
                        <small class="text-muted">'.($row->kota->name ?? '-').'</small>';
            })
            ->editColumn('jenis_anggota', function($row) {
                $jabatan = '';
                if($row->jenis_anggota == 'Pengurus') {
                    $badge = '<span class="badge bg-primary rounded-1 px-2">Pengurus</span>';
                    $jabatanAktif = $row->kepengurusanAktif;
                    if($jabatanAktif) $jabatan = '<div class="small text-muted mt-1">'.$jabatanAktif->jabatan.'</div>';
                } elseif($row->jenis_anggota == 'Remaja Mesjid') {
                    $badge = '<span class="badge bg-success rounded-1 px-2">Remaja Masjid</span>';
                } elseif($row->jenis_anggota == 'Alumni') {
                    $badge = '<span class="badge bg-info border text-dark bg-light rounded-1 px-2">Alumni</span>';
                } else {
                    $badge = '<span class="badge bg-secondary border text-dark bg-light rounded-1 px-2">Biasa</span>';
                }
                return $badge . $jabatan;
            })
            ->addColumn('masjid_aktif', function($row) {
                return '<span class="text-dark">'.($row->nama_masjid ?? '-').'</span>';
            })
            ->editColumn('status_verifikasi', function($row) {
                if($row->status_verifikasi == 'Verified') return '<span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Verified</span>';
                if($row->status_verifikasi == 'Pending Update') return '<span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-clock me-1"></i> Pending Update</span>';
                return '<span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-clock me-1"></i> Pending</span>';
            })
            ->addColumn('aksi', function($row) {
                $btn = '<div class="d-flex gap-1">';
                // Tombol View (Cyan)
                $btn .= '<a href="'.route('anggota.show', $row->id).'" class="btn btn-sm btn-info text-white" style="width:30px; height:30px; padding:4px;"><i class="fas fa-eye"></i></a>';
                // Tombol Edit (Kuning)
                $btn .= '<a href="'.route('anggota.edit', $row->id).'" class="btn btn-sm btn-warning text-dark" style="width:30px; height:30px; padding:4px;"><i class="fas fa-edit"></i></a>';

                // Tombol Cetak KTA / Delete
                if($row->status_verifikasi == 'Verified') {
                    $btn .= '<a href="'.route('anggota.print', $row->id).'" target="_blank" class="btn btn-sm btn-dark" style="width:30px; height:30px; padding:4px;" title="Cetak KTA"><i class="fas fa-id-card"></i></a>';
                } else {
                    $btn .= '<form action="'.route('anggota.destroy', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Hapus data ini?\')">
                                '.csrf_field().method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger" style="width:30px; height:30px; padding:4px;"><i class="fas fa-trash"></i></button>
                             </form>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->orderColumn('nia_urut', function ($query, $order) {
                $direction = strtolower($order) === 'desc' ? 'DESC' : 'ASC';
                $query->orderByRaw("anggota.nia IS NULL, CAST(SUBSTRING_INDEX(anggota.nia, '.', -1) AS UNSIGNED) {$direction}");
            })
            ->rawColumns(['no_urut', 'nia', 'nama_lengkap', 'wilayah', 'jenis_anggota', 'masjid_aktif', 'status_verifikasi', 'aksi'])
            ->make(true);
    }

    // --- 3. FITUR EXPORT DATA KTA ---
    public function exportKta(Request $request)
    {
        $provinsi_id = $request->provinsi_id;
        $kota_id = $request->kota_id;

        $user = Auth::user();
        if ($user->role == 'Admin PW') $provinsi_id = $user->provinsi_id;
        if ($user->role == 'Admin PD') {
            $provinsi_id = $user->provinsi_id;
            $kota_id = $user->kota_id;
        }

        $namaFile = 'Data_KTA_JPRMI_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new KtaExport($provinsi_id, $kota_id), $namaFile);
    }

    // --- 1. FITUR GET CITIES (AJAX UNTUK DROPDOWN) ---
    public function getCities(Request $request)
    {
        $provinsiId = (int) $request->provinsi_id;
        if (! $provinsiId) {
            return response()->json([]);
        }

        $provinsi = Cache::remember("wilayah:province:{$provinsiId}", now()->addHours(24), function () use ($provinsiId) {
            return Province::select('id', 'code')->find($provinsiId);
        });
        if (!$provinsi) return response()->json([]);

        $cities = Cache::remember("wilayah:cities:province:{$provinsiId}", now()->addHours(12), function () use ($provinsi) {
            return City::select('id', 'name')
                ->where('province_code', $provinsi->code)
                ->orderBy('name')
                ->get();
        });
        return response()->json($cities);
    }

    // AJAX: Ambil Kecamatan berdasarkan Kota
    public function getDistricts(Request $request)
    {
        $cityId = (int) $request->kota_id;
        if (! $cityId) {
            return response()->json();
        }

        $kota = Cache::remember("wilayah:city:{$cityId}", now()->addHours(24), function () use ($cityId) {
            return City::select('id', 'code')->find($cityId);
        });
        if (!$kota) return response()->json([]);

        $districts = Cache::remember("wilayah:districts:city:{$cityId}", now()->addHours(12), function () use ($kota) {
            return District::select('id', 'name')
                ->where('city_code', $kota->code)
                ->orderBy('name')
                ->get();
        });
        return response()->json($districts);
    }

    // --- 2. FORM TAMBAH DATA (CREATE) ---
    public function create()
    {
        $provinces = Cache::remember('wilayah:provinces:all', now()->addHours(24), function () {
            return Province::select('id', 'name', 'code')
                ->orderBy('name')
                ->get();
        });
        return view('admin.anggota.create', compact('provinces'));
    }

    // --- 3. PROSES SIMPAN DATA BARU (STORE) ---
    public function store(Request $request)
    {
        // Mengambil nama tabel asli dari Laravolt secara dinamis
        $tabelProvinsi = (new Province)->getTable();
        $tabelKota = (new City)->getTable();
        // Hitung umur berdasarkan tanggal lahir
        $umur = 0;
        if ($request->tanggal_lahir) {
            $umur = Carbon::parse($request->tanggal_lahir)->age;
        }

        // Aturan Dasar
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'jenis_anggota' => 'required|in:Remaja Mesjid,Pengurus,Alumni',
            // Gunakan variabel tabel di sini
            'provinsi_id' => 'required|exists:' . $tabelProvinsi . ',id',
            'kota_id' => 'required|exists:' . $tabelKota . ',id',
            'tahun_masuk' => 'required|digits:4',
            'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Jika Umur 17 ke atas (Wajib NIK & HP)
        if ($umur >= 17) {
            $rules['nik'] = 'required|digits:16|unique:anggota,nik';
            $rules['no_hp'] = 'required|string|min:10';
        } else {
            // Jika Umur di bawah 17 (Opsional)
            $rules['nik'] = 'nullable|digits:16|unique:anggota,nik';
            $rules['no_hp'] = 'nullable|string';

            // Otomatis paksa jenis anggota jadi Remaja Mesjid untuk yang di bawah umur
            $request->merge(['jenis_anggota' => 'Remaja Mesjid']);
        }

        $request->validate($rules);

        $fotoPath = 'default-avatar.png';
        if ($request->hasFile('foto_diri')) {
            $fotoPath = $request->file('foto_diri')->store('fotos', 'public');
        }
        $ktpPath = null;
        if ($request->hasFile('foto_ktp')) {
            $ktpFile = $request->file('foto_ktp');
            $filename = time() . '_KTP_' . $request->nik . '.' . $ktpFile->getClientOriginalExtension();

            // Pastikan folder ktp sudah ada di storage
            $destinationFolder = storage_path('app/public/ktp');
            if (!file_exists($destinationFolder)) {
                mkdir($destinationFolder, 0755, true);
            }

            // Inisialisasi Intervention Image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            // Baca KTP yang baru diupload
            $image = $manager->read($ktpFile->getRealPath());

            // Cek apakah file watermark sudah Anda siapkan di folder public/images/
            $watermarkPath = public_path('images/watermark-ktp.png');

            if (file_exists($watermarkPath)) {
                // Jika file watermark ada, tempelkan di tengah dengan opacity 50%
                $image->place($watermarkPath, 'center', 0, 0, 50);
            }

            // Simpan gambar (baik yang sudah di-watermark maupun belum)
            $image->save($destinationFolder . '/' . $filename);

            // Simpan path-nya untuk database
            $ktpPath = 'ktp/' . $filename;
        }

        $anggota = Anggota::create([
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'jenis_anggota' => $request->jenis_anggota,
            'provinsi_id' => $request->provinsi_id,
            'kota_id' => $request->kota_id,
            'kecamatan_id' => $request->kecamatan_id,
            'alamat_lengkap' => $request->alamat_lengkap,
            'alamat_domisili' => $request->alamat_domisili ?? $request->alamat_lengkap, // Jika kosong, samakan dengan KTP
            'nama_masjid' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->nama_masjid : null,
            'alamat_masjid' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->alamat_masjid : null,
            'tahun_masuk' => $request->tahun_masuk,
            'foto_diri' => $fotoPath,
            'foto_ktp' => $ktpPath,
            'status_verifikasi' => 'Pending',
            'created_by' => Auth::id(),
        ]);
        // --- LOGIKA BARU: OTOMATISASI JABATAN "ANGGOTA" ---
        // Jika yang didaftarkan BUKAN Remaja Masjid (berarti Pengurus atau Alumni)
        if ($request->jenis_anggota != 'Remaja Mesjid') {

            // 1. Tentukan Tingkat berdasarkan Level Admin yang mendaftarkan
            $userRole = Auth::user()->role;
            $tingkatDefault = 'Pengurus Daerah (PD)'; // Default terendah

            if ($userRole == 'Admin PP') {
                $tingkatDefault = 'Pengurus Pusat (PP)';
            } elseif ($userRole == 'Admin PW') {
                $tingkatDefault = 'Pengurus Wilayah (PW)';
            }

            // 2. Buat Riwayat Jabatannya di database
            \App\Models\PengurusStruktural::create([
                'anggota_id' => $anggota->id,
                'tingkat' => $tingkatDefault,
                'jabatan' => 'Anggota',       // Set otomatis sebagai "Anggota"
                'nama_bidang' => null,        // Tanpa bidang spesifik
                'periode_awal' => date('Y'),
                'periode_akhir' => date('Y') + 2, // Default masa bakti 2 tahun ke depan

                // Jika dia 'Pengurus', maka jabatannya Aktif. Jika 'Alumni', jadikan Demisioner (False)
                'is_active' => ($request->jenis_anggota == 'Pengurus') ? true : false,
            ]);
        }

        return redirect()->route('anggota.index')->with('success', 'Anggota baru berhasil ditambahkan dan berstatus PENDING.');
    }

    // --- 4. FORM EDIT DATA ---
    public function edit($id)
    {
        $anggota = Anggota::findOrFail($id);
        $provinces = Cache::remember('wilayah:provinces:all', now()->addHours(24), function () {
            return Province::select('id', 'name', 'code')
                ->orderBy('name')
                ->get();
        });

        // Ambil kota berdasarkan provinsi anggota tersebut
        $provinsiId = (int) $anggota->provinsi_id;
        $cities = Cache::remember("wilayah:cities:province:{$provinsiId}", now()->addHours(12), function () use ($anggota) {
            return City::select('id', 'name')
                ->where('province_code', $anggota->provinsi->code)
                ->orderBy('name')
                ->get();
        });

        return view('admin.anggota.edit', compact('anggota', 'provinces', 'cities'));
    }

    // --- 5. PROSES UPDATE (DENGAN LOGIC DRAFT) ---
    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);
        $user = Auth::user();

        // Mengambil nama tabel asli dari Laravolt
        $tabelProvinsi = (new Province)->getTable();
        $tabelKota = (new City)->getTable();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            // Gunakan variabel tabel di sini
            'provinsi_id' => 'required|exists:' . $tabelProvinsi . ',id',
            'kota_id' => 'required|exists:' . $tabelKota . ',id',
            'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'alamat_lengkap' => 'required|string'
        ]);

        $fotoPath = $anggota->foto_diri;
        if ($request->hasFile('foto_diri')) {
            $fotoPath = $request->file('foto_diri')->store('fotos', 'public');
        }
        $ktpPath = $anggota->foto_ktp; // Simpan path lama
        // $ktpPath = null;
        if ($request->hasFile('foto_ktp')) {
            $ktpFile = $request->file('foto_ktp');
            $filename = time() . '_KTP_' . $request->nik . '.' . $ktpFile->getClientOriginalExtension();

            // Pastikan folder ktp sudah ada di storage
            $destinationFolder = storage_path('app/public/ktp');
            if (!file_exists($destinationFolder)) {
                mkdir($destinationFolder, 0755, true);
            }

            // Inisialisasi Intervention Image
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            // Baca KTP yang baru diupload
            $image = $manager->read($ktpFile->getRealPath());

            // Cek apakah file watermark sudah Anda siapkan di folder public/images/
            $watermarkPath = public_path('images/watermark-ktp.png');

            if (file_exists($watermarkPath)) {
                // Jika file watermark ada, tempelkan di tengah dengan opacity 50%
                $image->place($watermarkPath, 'center', 0, 0, 50);
            }

            // Simpan gambar (baik yang sudah di-watermark maupun belum)
            $image->save($destinationFolder . '/' . $filename);

            // Simpan path-nya untuk database
            $ktpPath = 'ktp/' . $filename;
        }

        // RULE: Jika Admin PD yang edit, masuk ke tabel Draft
        if ($user->role == 'Admin PD' && $anggota->status_verifikasi == 'Verified') {

            AnggotaDraft::create([
                'anggota_id' => $anggota->id,
                'jenis_anggota_baru' => $request->jenis_anggota,
                'nama_lengkap_baru' => $request->nama_lengkap,
                'alamat_lengkap_baru' => $request->alamat_lengkap,
                'foto_diri_baru' => $fotoPath,
                'diajukan_oleh' => $user->id,
                'status_approval' => 'Menunggu',
                'nama_masjid_baru' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->nama_masjid : null,
                'alamat_masjid_baru' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->alamat_masjid : null,
            ]);

            $anggota->update(['status_verifikasi' => 'Pending Update']);
            return redirect()->route('anggota.index')->with('success', 'Perubahan diajukan dan menunggu persetujuan Admin Wilayah/Pusat.');

        } else {
            // Jika Admin PW / PP, langsung timpa data aslinya
            $anggota->update([
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'jenis_anggota' => $request->jenis_anggota,
                'provinsi_id' => $request->provinsi_id,
                'kota_id' => $request->kota_id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'foto_diri' => $fotoPath,
                'foto_ktp' => $ktpPath,
                'nama_masjid' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->nama_masjid : null,
                'alamat_masjid' => $request->jenis_anggota == 'Remaja Mesjid' ? $request->alamat_masjid : null,
            ]);

            // Regenerate NIA jika domisili pindah
            $anggota->syncNiaDenganDomisili();

            return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
        }
    }

    // --- FITUR SET PENGURUS STRUKTURAL ---
    public function storePengurus(Request $request, $anggota_id)
    {
        $request->validate([
            'tingkat' => 'required|string',
            'jabatan' => 'required|string|max:255',
            'periode_awal' => 'required|digits:4',
            'periode_akhir' => 'required|digits:4',
        ]);

        $isActive = $request->has('is_active');

        // Logika untuk Nama Bidang (Jika pilih 'Other', ambil dari text input)
        $nama_bidang = $request->nama_bidang;
        if ($nama_bidang === 'Other') {
            $nama_bidang = $request->bidang_lainnya;
        }

        if ($isActive) {
            PengurusStruktural::where('anggota_id', $anggota_id)->update(['is_active' => false]);
        }

        PengurusStruktural::create([
            'anggota_id' => $anggota_id,
            'tingkat' => $request->tingkat,
            'jabatan' => $request->jabatan,
            'nama_bidang' => $nama_bidang, // Simpan nama bidang di sini
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'is_active' => $isActive,
        ]);

        // --- LOGIKA EVALUASI STATUS ANGGOTA ---
        if ($isActive) {
            // Jika dia menjabat, pastikan statusnya Pengurus
            Anggota::where('id', $anggota_id)->update(['jenis_anggota' => 'Pengurus']);
        } else {
            // Jika form tidak dicentang 'aktif', cek apakah dia punya jabatan aktif lain?
            $hasActive = PengurusStruktural::where('anggota_id', $anggota_id)->where('is_active', true)->exists();
            if (!$hasActive) {
                // Jika tidak ada sama sekali, jadikan Alumni
                Anggota::where('id', $anggota_id)->update(['jenis_anggota' => 'Alumni']);
            }
        }

        return redirect()->back()->with('success', 'Jabatan struktural berhasil ditambahkan.');
    }

    // --- FITUR SELESAI MENJABAT (DEMISIONER) ---
    public function nonaktifkanPengurus($id)
    {
        $jabatan = PengurusStruktural::findOrFail($id);
        $anggota_id = $jabatan->anggota_id;

        // Ubah jabatan ini menjadi tidak aktif
        $jabatan->update(['is_active' => false]);

        // Cek apakah anggota ini masih punya jabatan aktif yang lain?
        $hasActive = PengurusStruktural::where('anggota_id', $anggota_id)->where('is_active', true)->exists();

        // Jika sudah tidak punya jabatan aktif sama sekali, ubah statusnya jadi Alumni
        if (!$hasActive) {
            Anggota::where('id', $anggota_id)->update(['jenis_anggota' => 'Alumni']);
        }

        return redirect()->back()->with('success', 'Jabatan diselesaikan (Demisioner). Status anggota telah diperbarui otomatis.');
    }

    // Fungsi untuk memanggil halaman ID Card
    public function printKta($id)
    {
        $anggota = Anggota::findOrFail($id);

        if($anggota->status_verifikasi != 'Verified') {
            abort(403, 'Anggota belum diverifikasi, KTA tidak dapat dicetak.');
        }

        return view('admin.anggota.print', compact('anggota'));
    }

    public function show($id)
    {
        // Ambil data anggota beserta relasi provinsi dan kota
        $anggota = Anggota::with(['provinsi', 'kota', 'createdBy'])->findOrFail($id);

        // Keamanan: Cek apakah Admin yang login berhak melihat data ini
        $user = Auth::user();
        if ($user->role == 'Admin PW' && $anggota->provinsi_id != $user->provinsi_id) {
            abort(403, 'Anda tidak berhak melihat anggota dari wilayah lain.');
        }
        if ($user->role == 'Admin PD' && $anggota->kota_id != $user->kota_id) {
            abort(403, 'Anda tidak berhak melihat anggota dari kota lain.');
        }

        return view('admin.anggota.show', compact('anggota'));
    }

    // Menampilkan halaman form Import
    public function importForm()
    {
        return view('admin.anggota.import');
    }

    // Memproses file CSV yang diupload
    public function importData(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt,xls,xlsx|max:10240', // Max 10MB
        ]);

        try {
            // Eksekusi Import
            Excel::import(new AnggotaImport, $request->file('file_csv'));

            return redirect()->route('anggota.index')->with('success', 'Data Anggota berhasil di-import dari file Excel/CSV.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    // AJAX: Cek NIK
    public function checkNik(Request $request)
    {
        $anggota = Anggota::where('nik', $request->nik)->first();
        if ($anggota) {
            return response()->json(['exists' => true, 'id' => $anggota->id]);
        }
        return response()->json(['exists' => false]);
    }
}
