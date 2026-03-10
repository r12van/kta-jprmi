@extends('layouts.admin')

@section('title', 'Persetujuan Data - JPRMI')
@section('page_heading', 'Persetujuan Data (Approval)')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <ul class="nav nav-tabs card-header-tabs" id="approvalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-success fw-bold" id="baru-tab" data-bs-toggle="tab" data-bs-target="#baru" type="button" role="tab">
                    <i class="fas fa-user-plus me-1"></i> Anggota Baru
                    @if($pendingMembers->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingMembers->count() }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-primary fw-bold" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                    <i class="fas fa-edit me-1"></i> Ajuan Perubahan Data
                    @if($pendingDrafts->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingDrafts->count() }}</span>
                    @endif
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="approvalTabsContent">

            <div class="tab-pane fade show active p-4" id="baru" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <th>Wilayah</th>
                                <th>Jenis Anggota</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingMembers as $member)
                            <tr>
                                <td class="fw-bold">{{ $member->nama_lengkap }}</td>
                                <td>{{ $member->nik }}</td>
                                <td>
                                    <div>{{ $member->kota->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $member->provinsi->name ?? '-' }}</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $member->jenis_anggota }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('approval.member.approve', $member->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui anggota ini dan terbitkan NIA?');"><i class="fas fa-check me-1"></i> Setujui</button>
                                        </form>
                                        <form action="{{ route('approval.member.reject', $member->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak dan hapus data ini?');"><i class="fas fa-times me-1"></i> Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada antrean anggota baru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="edit" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-start border-primary border-4">
                        <thead class="table-light">
                            <tr>
                                <th>Anggota</th>
                                <th>Data Lama</th>
                                <th>Ajuan Data Baru</th>
                                <th>Diajukan Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDrafts as $draft)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $draft->anggota->nama_lengkap }}</div>
                                    <small class="text-success fw-bold">{{ $draft->anggota->nia }}</small>
                                </td>
                                <td class="text-muted small">
                                    @if($draft->nama_lengkap_baru) <div>Nama: {{ $draft->anggota->nama_lengkap }}</div> @endif
                                    @if($draft->alamat_lengkap_baru) <div>Alamat: {{ $draft->anggota->alamat_lengkap }}</div> @endif
                                </td>
                                <td class="text-primary small fw-bold">
                                    @if($draft->nama_lengkap_baru) <div>Nama: {{ $draft->nama_lengkap_baru }}</div> @endif
                                    @if($draft->alamat_lengkap_baru) <div>Alamat: {{ $draft->alamat_lengkap_baru }}</div> @endif
                                    @if($draft->foto_diri_baru && $draft->foto_diri_baru != $draft->anggota->foto_diri) <div class="text-warning"><i class="fas fa-image"></i> Update Foto</div> @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><i class="fas fa-user-shield me-1"></i> {{ $draft->diajukanOleh->nama ?? 'Admin PD' }}</span>
                                    <div class="small text-muted mt-1">{{ $draft->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('approval.draft.approve', $draft->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Setujui dan timpa data lama dengan data baru ini?');"><i class="fas fa-check me-1"></i> ACC</button>
                                        </form>
                                        <form action="{{ route('approval.draft.reject', $draft->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak ajuan perubahan data ini?');"><i class="fas fa-times me-1"></i> Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada ajuan perubahan data dari Admin Daerah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
