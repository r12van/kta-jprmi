<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    // 1. DAFTAR AKUN
    public function index()
    {
        $user = Auth::user();

        // Mulai query
        $query = User::with(['provinsi', 'kota'])->where('id', '!=', $user->id); // Jangan tampilkan diri sendiri

        // Jika yang login Admin PW, dia hanya bisa melihat Admin PD di provinsinya
        if ($user->role == 'Admin PW') {
            $query->where('role', 'Admin PD')
                  ->where('provinsi_id', $user->provinsi_id);
        }

        $users = $query->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    // 2. FORM TAMBAH AKUN
    public function create()
    {
        $user = Auth::user();

        // Admin PW hanya bisa melihat provinsinya sendiri
        if ($user->role == 'Admin PW') {
            $provinces = Cache::remember("wilayah:provinces:pw:{$user->provinsi_id}", now()->addHours(24), function () use ($user) {
                return Province::select('id', 'name', 'code')
                    ->where('id', $user->provinsi_id)
                    ->orderBy('name')
                    ->get();
            });
        } else {
            $provinces = Cache::remember('wilayah:provinces:all', now()->addHours(24), function () {
                return Province::select('id', 'name', 'code')
                    ->orderBy('name')
                    ->get();
            });
        }

        return view('admin.users.create', compact('provinces'));
    }

    // 3. SIMPAN AKUN BARU
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $tabelProvinsi = (new Province)->getTable();
        $tabelKota = (new City)->getTable();

        // Validasi Dasar
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:Admin PP,Admin PW,Admin PD',
        ];

        // Validasi Wilayah Berdasarkan Role yang dipilih
        if ($request->role == 'Admin PW') {
            $rules['provinsi_id'] = 'required|exists:' . $tabelProvinsi . ',id';
        } elseif ($request->role == 'Admin PD') {
            $rules['provinsi_id'] = 'required|exists:' . $tabelProvinsi . ',id';
            $rules['kota_id'] = 'required|exists:' . $tabelKota . ',id';
        }

        $request->validate($rules);

        // Proteksi Keamanan: Admin PW tidak boleh membuat Admin PP atau Admin PW lain
        if ($currentUser->role == 'Admin PW' && in_array($request->role, ['Admin PP', 'Admin PW'])) {
            abort(403, 'Anda tidak memiliki hak untuk membuat akun dengan level tersebut.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'provinsi_id' => $request->role != 'Admin PP' ? $request->provinsi_id : null,
            'kota_id' => $request->role == 'Admin PD' ? $request->kota_id : null,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun Admin berhasil dibuat.');
    }

    // 4. HAPUS AKUN
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }
}
