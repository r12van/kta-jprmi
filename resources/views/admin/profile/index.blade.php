@extends('layouts.admin')

@section('title', 'Profil Saya - JPRMI')
@section('page_heading', 'Pengaturan Profil')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-top border-success border-3">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->email }}</p>

                <ul class="list-group list-group-flush text-start mt-4">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Level Akses</span>
                        <span class="badge bg-dark">{{ $user->role }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Provinsi</span>
                        <span class="fw-bold small text-end">{{ $user->provinsi->name ?? 'Nasional' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kota/Kabupaten</span>
                        <span class="fw-bold small text-end">{{ $user->kota->name ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-user-edit me-2 text-success"></i>Update Data & Password</h6>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom">Informasi Dasar</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom">Ganti Password <span class="fw-normal small text-danger">(Kosongkan jika tidak ingin mengganti)</span></h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama Anda">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Ulangi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
