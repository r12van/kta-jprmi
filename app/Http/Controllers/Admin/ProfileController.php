<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Tampilkan Halaman Profil
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    // Proses Update Profil & Password
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            // Pastikan email unik, kecuali untuk email dia sendiri
            'email' => 'required|email|unique:users,email,' . $user->id,

            // Validasi password (opsional, hanya jika diisi)
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed', // butuh input 'new_password_confirmation'
        ]);

        // 1. Update Nama & Email
        // Catatan: Jika di tabel Anda kolomnya bernama 'name', ubah tulisan $user->nama di bawah menjadi $user->name
        $user->name = $request->nama;
        $user->email = $request->email;

        // 2. Update Password (Jika form password diisi)
        if ($request->filled('new_password')) {
            // Cek apakah password lama yang dimasukkan benar
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah!']);
            }

            // Jika benar, ganti dengan password baru
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil dan kredensial Anda berhasil diperbarui!');
    }
}
