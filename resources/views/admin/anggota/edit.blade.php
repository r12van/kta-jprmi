@extends('layouts.admin')

@section('title', 'Edit Anggota - JPRMI')
@section('page_heading', 'Edit Data Anggota')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-success"><i class="fas fa-user-edit me-2"></i>Edit Form: {{ $anggota->nama_lengkap }}</h6>
        <span class="badge bg-secondary">NIA: {{ $anggota->nia ?? 'Belum ada' }}</span>
    </div>
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Gagal Menyimpan:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(auth()->user()->role == 'Admin PD')
            <div class="alert alert-warning small">
                <i class="fas fa-info-circle me-1"></i> <b>Perhatian:</b> Perubahan yang Anda lakukan akan berstatus <i>Pending Update</i> hingga disetujui oleh Admin Wilayah/Pusat.
            </div>
        @endif

        <form action="{{ route('anggota.update', $anggota->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">NIK</label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $anggota->nik) }}" required>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}">
                    @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('Y-m-d') : '') }}">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                        <option value="L" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) == 'L')>Laki-Laki</option>
                        <option value="P" @selected(old('jenis_kelamin', $anggota->jenis_kelamin) == 'P')>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $anggota->no_hp) }}">
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Jenis Anggota</label>
                    <select id="jenis_anggota_select" name="jenis_anggota" class="form-select @error('jenis_anggota') is-invalid @enderror" required>
                        <option value="Remaja Mesjid" @selected(old('jenis_anggota', $anggota->jenis_anggota) == 'Remaja Mesjid')>Remaja Mesjid</option>
                        <option value="Pengurus" @selected(old('jenis_anggota', $anggota->jenis_anggota) == 'Pengurus')>Pengurus</option>
                        <option value="Alumni" @selected(old('jenis_anggota', $anggota->jenis_anggota) == 'Alumni')>Alumni</option>
                    </select>
                    @error('jenis_anggota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div id="masjid_container" class="bg-light p-3 rounded border mb-3" style="{{ old('jenis_anggota', $anggota->jenis_anggota) == 'Remaja Mesjid' ? 'display: block;' : 'display: none;' }}">
                <h6 class="fw-bold text-info"><i class="fas fa-mosque me-2"></i>Data Asal Masjid</h6>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label fw-bold small">Nama Masjid</label>
                        <input type="text" name="nama_masjid" class="form-control @error('nama_masjid') is-invalid @enderror" value="{{ old('nama_masjid', $anggota->nama_masjid) }}">
                        @error('nama_masjid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-7 mb-2">
                        <label class="form-label fw-bold small">Alamat Masjid</label>
                        <input type="text" name="alamat_masjid" class="form-control @error('alamat_masjid') is-invalid @enderror" value="{{ old('alamat_masjid', $anggota->alamat_masjid) }}">
                        @error('alamat_masjid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Provinsi</label>
                    <select name="provinsi_id" id="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" required>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" @selected(old('provinsi_id', $anggota->provinsi_id) == $prov->id)>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                    @error('provinsi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Kota/Kabupaten</label>
                    <select name="kota_id" id="kota_id" class="form-select @error('kota_id') is-invalid @enderror" required>
                        @foreach($cities as $kota)
                            <option value="{{ $kota->id }}" @selected(old('kota_id', $anggota->kota_id) == $kota->id)>{{ $kota->name }}</option>
                        @endforeach
                    </select>
                    @error('kota_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" rows="3" class="form-control @error('alamat_lengkap') is-invalid @enderror">{{ old('alamat_lengkap', $anggota->alamat_lengkap) }}</textarea>
                @error('alamat_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ganti Foto Diri</label>
                <input type="file" name="foto_diri" class="form-control @error('foto_diri') is-invalid @enderror" accept="image/*">
                @error('foto_diri')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Scan / Foto KTP</label>
                <input type="file" name="foto_ktp" class="form-control @error('foto_ktp') is-invalid @enderror" accept="image/*">
                @error('foto_ktp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen KTP.</small>
            </div>
            <div class="d-flex justify-content-end border-top pt-3">
                <a href="{{ route('anggota.show', $anggota->id) }}" class="btn btn-light border me-2">Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#provinsi_id').on('change', function () {
            const provId = $(this).val();
            const $kotaSelect = $('#kota_id');
            $kotaSelect.html('<option value="">Memuat data...</option>');

            if (provId) {
                $.getJSON('/admin/get-cities', { provinsi_id: provId }, function (data) {
                    $kotaSelect.html('<option value="">-- Pilih Kota/Kabupaten --</option>');
                    $.each(data, function (_, kota) {
                        $kotaSelect.append('<option value="' + kota.id + '">' + kota.name + '</option>');
                    });
                });
            }
        });

        $('select[name="jenis_anggota"]').on('change', function () {
            $('#masjid_container').toggle($(this).val() === 'Remaja Mesjid');
        });
    });
</script>
@endpush

