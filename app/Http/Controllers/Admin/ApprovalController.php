<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\AnggotaDraft;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    // 1. Tampilkan Halaman Daftar Antrean (Pending & Draft)
    public function index()
    {
        $user = Auth::user();

        // Proteksi: Hanya PW dan PP yang boleh mengakses halaman ini
        if (!in_array($user->role, ['Admin PW', 'Admin PP'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman Persetujuan.');
        }

        // Query Pendaftar Baru (Pending)
        $queryPending = Anggota::with(['provinsi', 'kota'])->where('status_verifikasi', 'Pending');

        // Query Perubahan Data (Draft)
        $queryDraft = AnggotaDraft::with(['anggota', 'diajukanOleh'])->where('status_approval', 'Menunggu');

        // Jika Admin PW, filter hanya antrean di provinsinya saja
        if ($user->role == 'Admin PW') {
            $queryPending->where('provinsi_id', $user->provinsi_id);
            $queryDraft->whereHas('anggota', function($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        $pendingMembers = $queryPending->latest()->get();
        $pendingDrafts = $queryDraft->latest()->get();

        return view('admin.approval.index', compact('pendingMembers', 'pendingDrafts'));
    }

    // 2. Setujui Anggota Baru (Generate NIA)
    public function approveMember($id)
    {
        $anggota = Anggota::findOrFail($id);

        // Panggil fungsi pembuat NIA yang sudah kita rakit di Model
        $nia = $anggota->generateNia();

        $anggota->verified_by = Auth::id();
        $anggota->save();

        return redirect()->back()->with('success', "Anggota baru berhasil disetujui. NIA {$nia} telah diterbitkan.");
    }

    // 3. Tolak Anggota Baru
    public function rejectMember($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete(); // Atau bisa diubah statusnya jadi 'Ditolak' jika ingin menyimpan history

        return redirect()->back()->with('success', 'Pendaftaran anggota baru telah ditolak dan dihapus.');
    }

    // 4. Setujui Perubahan Data (Draft) dari Admin PD
    public function approveDraft($id)
    {
        $draft = AnggotaDraft::findOrFail($id);

        // Gunakan fungsi approveDraft yang sudah kita buat di Model AnggotaDraft
        $draft->approveDraft(Auth::id());

        // Jika ada perubahan domisili (meskipun lewat draft form), sinkronkan NIA
        // Karena form draft kita saat ini baru nama, alamat, foto, kita panggil jaga-jaga
        $draft->anggota->syncNiaDenganDomisili();

        return redirect()->back()->with('success', 'Perubahan data anggota berhasil disetujui dan diterapkan.');
    }

    // 5. Tolak Perubahan Data (Draft)
    public function rejectDraft($id)
    {
        $draft = AnggotaDraft::findOrFail($id);
        $draft->update([
            'status_approval' => 'Ditolak',
            'reviewed_by' => Auth::id()
        ]);

        // Kembalikan status anggota ke Verified (karena editnya batal)
        $draft->anggota->update(['status_verifikasi' => 'Verified']);

        return redirect()->back()->with('success', 'Ajuan perubahan data telah ditolak.');
    }
}
