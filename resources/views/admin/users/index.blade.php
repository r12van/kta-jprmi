@extends('layouts.admin')

@section('title', 'Manajemen Akun Admin - JPRMI')
@section('page_heading', 'Manajemen Akun Admin')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-success"><i class="fas fa-users-cog me-2"></i>Daftar Akun Admin</h6>
        <a href="{{ route('users.create') }}" class="btn btn-success btn-sm"><i class="fas fa-user-plus me-1"></i> Buat Akun Admin</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Admin</th>
                        <th>Email / Login</th>
                        <th>Level Akses (Role)</th>
                        <th>Wilayah Tugas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="fw-bold">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if($u->role == 'Admin PP') <span class="badge bg-danger">Admin Pusat</span>
                            @elseif($u->role == 'Admin PW') <span class="badge bg-primary">Admin Wilayah</span>
                            @else <span class="badge bg-success">Admin Daerah</span>
                            @endif
                        </td>
                        <td>
                            @if($u->role == 'Admin PP')
                                <span class="text-muted">Nasional (Seluruh Indonesia)</span>
                            @elseif($u->role == 'Admin PW')
                                <span class="fw-bold">{{ $u->provinsi->name ?? '-' }}</span>
                            @else
                                <span class="fw-bold">{{ $u->kota->name ?? '-' }}</span><br>
                                <small class="text-muted">{{ $u->provinsi->name ?? '-' }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mencabut akses dan menghapus akun ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada akun admin lain yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
