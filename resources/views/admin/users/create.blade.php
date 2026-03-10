@extends('layouts.admin')

@section('title', 'Buat Akun Admin - JPRMI')
@section('page_heading', 'Buat Akun Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success"><i class="fas fa-user-plus me-2"></i>Form Pembuatan Akun Akses</h6>
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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap Admin <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Misal: Budi Santoso" value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Email (Untuk Login) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="admin.jabar@jprmi.id" value="{{ old('email') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Level Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" id="role_select" class="form-select" required>
                            <option value="">-- Pilih Level Akses --</option>
                            @if(auth()->user()->role == 'Admin PP')
                                <option value="Admin PP" @selected(old('role') == 'Admin PP')>Admin Pusat (Nasional)</option>
                                <option value="Admin PW" @selected(old('role') == 'Admin PW')>Admin Wilayah (Provinsi)</option>
                            @endif
                            <option value="Admin PD" @selected(old('role') == 'Admin PD')>Admin Daerah (Kota/Kabupaten)</option>
                        </select>
                    </div>

                    <div id="wilayah_container" style="display: none;">
                        <div class="mb-3" id="provinsi_container">
                            <label class="form-label fw-bold">Pilih Provinsi Tugas <span class="text-danger">*</span></label>
                            <select name="provinsi_id" id="provinsi_id" class="form-select">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->id }}" @selected(old('provinsi_id') == $prov->id)>{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="kota_container">
                            <label class="form-label fw-bold">Pilih Kota/Kab Tugas <span class="text-danger">*</span></label>
                            <select name="kota_id" id="kota_id" class="form-select">
                                <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                                @if(old('kota_id'))
                                    <option value="{{ old('kota_id') }}" selected>{{ old('kota_id') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-light border me-2">Batal</a>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Buat Akun</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        function updateWilayahByRole() {
            const role = $('#role_select').val();

            if (role === 'Admin PP' || role === '') {
                $('#wilayah_container').hide();
            } else if (role === 'Admin PW') {
                $('#wilayah_container').show();
                $('#kota_container').hide();
            } else if (role === 'Admin PD') {
                $('#wilayah_container').show();
                $('#kota_container').show();
            }
        }

        $('#role_select').on('change', updateWilayahByRole);
        updateWilayahByRole();

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
    });
</script>
@endpush

