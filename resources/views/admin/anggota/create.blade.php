@extends('layouts.admin')

@section('title', 'Tambah Anggota Baru - JPRMI')
@section('page_heading', 'Formulir Pendaftaran Anggota')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-xl-10 col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success"><i class="fas fa-user-plus me-2"></i>Tambah Data Anggota Baru</h6>
            </div>
            <div class="card-body p-4 p-md-5">

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Mohon periksa kembali isian Anda:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('anggota.store') }}" method="POST" enctype="multipart/form-data" id="formAnggota">
                    @csrf

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom"><i class="fas fa-user me-2"></i>Data Pribadi</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                            <small id="umur_info" class="text-success fw-bold d-none mt-1"></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor Induk Kependudukan (NIK) <span class="text-danger req-star">*</span></label>
                            <input type="number" name="nik" id="nik_input" class="form-control" placeholder="16 Digit NIK" value="{{ old('nik') }}" required>

                            <div id="nik_warning" class="alert alert-warning mt-2 d-none p-2 small">
                                <i class="fas fa-exclamation-triangle"></i> NIK sudah terdaftar!
                                <a href="#" id="btn_update_nik" class="btn btn-sm btn-primary ms-2 fw-bold">Buka Profil Anggota Ini</a>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Misal: Ahmad Fulan" value="{{ old('nama_lengkap') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten Lahir" value="{{ old('tempat_lahir') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor WhatsApp / HP <span class="text-danger req-star">*</span></label>
                            <input type="number" name="no_hp" id="no_hp" class="form-control" placeholder="Mulai dengan 08..." value="{{ old('no_hp') }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom mt-4"><i class="fas fa-users me-2"></i>Status Keanggotaan JPRMI</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Anggota <span class="text-danger">*</span></label>
                            <select name="jenis_anggota" id="jenis_anggota" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Pengurus" {{ old('jenis_anggota') == 'Pengurus' ? 'selected' : '' }}>Pengurus (PP/PW/PD/PC/PR)</option>
                                <option value="Remaja Mesjid" {{ old('jenis_anggota') == 'Remaja Mesjid' ? 'selected' : '' }}>Anggota Remaja Masjid</option>
                                <option value="Alumni" {{ old('jenis_anggota') == 'Alumni' ? 'selected' : '' }}>Alumni / Biasa</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tahun Bergabung <span class="text-danger">*</span></label>
                            <input type="number" name="tahun_masuk" class="form-control" value="{{ old('tahun_masuk', date('Y')) }}" required>
                        </div>

                        <div class="col-md-12">
                            <div id="masjid_container" class="bg-info bg-opacity-10 p-3 rounded border border-info mb-3" style="display: none;">
                                <h6 class="fw-bold text-info mb-3"><i class="fas fa-mosque me-2"></i>Data Asal Masjid (Khusus Remaja Masjid)</h6>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="form-label fw-bold small">Nama Masjid</label>
                                        <input type="text" name="nama_masjid" class="form-control" placeholder="Contoh: Masjid Raya Al-Jabbar" value="{{ old('nama_masjid') }}">
                                    </div>
                                    <div class="col-md-7 mb-2">
                                        <label class="form-label fw-bold small">Alamat Masjid</label>
                                        <input type="text" name="alamat_masjid" class="form-control" placeholder="Jalan, Kelurahan, Kecamatan" value="{{ old('alamat_masjid') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom mt-4"><i class="fas fa-id-card-alt me-2"></i>Alamat Sesuai KTP</h6>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Provinsi (KTP) <span class="text-danger">*</span></label>
                            <select name="ktp_provinsi_id" id="ktp_provinsi" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Kota/Kabupaten (KTP) <span class="text-danger">*</span></label>
                            <select name="ktp_kota_id" id="ktp_kota" class="form-select" required>
                                <option value="">-- Pilih Provinsi Dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Kecamatan (KTP)</label>
                            <select name="ktp_kecamatan_id" id="ktp_kecamatan" class="form-select">
                                <option value="">-- Pilih Kota Dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Detail Alamat KTP (Jalan/RT/RW/Desa) <span class="text-danger">*</span></label>
                            <textarea name="alamat_lengkap" id="alamat_ktp" class="form-control" rows="2" required>{{ old('alamat_lengkap') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mb-3 mt-5 border-bottom pb-2">
                        <h6 class="fw-bold text-success m-0"><i class="fas fa-home me-2"></i>Alamat Domisili (Penentu Wilayah JPRMI)</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sama_dengan_ktp" id="sama_dengan_ktp">
                            <label class="form-check-label text-dark fw-bold small" for="sama_dengan_ktp">Sama dengan KTP</label>
                        </div>
                    </div>

                    <div class="row mb-4" id="blok_domisili">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Provinsi (Domisili) <span class="text-danger">*</span></label>
                            <select name="provinsi_id" id="domisili_provinsi" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Kota/Kabupaten (Domisili) <span class="text-danger">*</span></label>
                            <select name="kota_id" id="domisili_kota" class="form-select" required>
                                <option value="">-- Pilih Provinsi Dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Kecamatan (Domisili) <span class="text-danger">*</span></label>
                            <select name="kecamatan_id" id="domisili_kecamatan" class="form-select" required>
                                <option value="">-- Pilih Kota Dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Detail Alamat Domisili <span class="text-danger">*</span></label>
                            <textarea name="alamat_domisili" id="alamat_domisili" class="form-control" rows="2" required>{{ old('alamat_domisili') }}</textarea>
                            <small class="text-success fw-bold mt-1 d-block"><i class="fas fa-info-circle"></i> Catatan: Data Provinsi, Kota, dan Kecamatan domisili di atas akan digunakan sebagai penempatan data wilayah kepengurusan (PW/PD/PC).</small>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted mb-3 pb-2 border-bottom mt-4"><i class="fas fa-file-upload me-2"></i>Dokumen Lampiran (Opsional)</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Foto Diri (Pas Foto)</label>
                            <input type="file" name="foto_diri" class="form-control" accept="image/*">
                            <small class="text-muted">Digunakan untuk tampilan KTA. Maksimal 2MB.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary">Scan / Foto KTP</label>
                            <input type="file" name="foto_ktp" class="form-control border-primary" accept="image/*">
                            <small class="text-primary">Digunakan admin untuk validasi NIK. Otomatis diberi Watermark JPRMI.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-4 mt-4">
                        <a href="{{ route('anggota.index') }}" class="btn btn-light border me-2 px-4 fw-bold">Batal</a>
                        <button type="submit" id="btn_submit" class="btn btn-success px-5 fw-bold"><i class="fas fa-save me-2"></i> Simpan Pendaftaran</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        // --- 1. AJAX CEK NIK KEMBAR ---
        $('#nik_input').on('keyup blur', function() {
            let nik = $(this).val();
            if(nik.length >= 16) {
                $.ajax({
                    url: "{{ route('anggota.checkNik') }}",
                    data: { nik: nik },
                    success: function(res) {
                        if(res.exists) {
                            $('#nik_warning').removeClass('d-none');
                            $('#btn_update_nik').attr('href', '/admin/anggota/' + res.id + '/edit');
                            $('#btn_submit').prop('disabled', true); // Kunci tombol simpan
                        } else {
                            $('#nik_warning').addClass('d-none');
                            $('#btn_submit').prop('disabled', false); // Buka tombol simpan
                        }
                    }
                });
            }
        });

        // --- 2. LOGIKA HITUNG UMUR (DEWASA vs DI BAWAH UMUR) ---
        $('#tanggal_lahir').change(function() {
            let dob = new Date($(this).val());
            let today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            let m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--; // Hitung umur akurat
            }

            $('#umur_info').removeClass('d-none').html('<i class="fas fa-check-circle"></i> Usia pendaftar: ' + age + ' Tahun');

            if(age < 17) {
                // Di bawah umur: NIK & HP opsional
                $('#nik_input, #no_hp').removeAttr('required');
                $('.req-star').hide();

                // Paksa pilih Remaja Mesjid
                $('#jenis_anggota').val('Remaja Mesjid').trigger('change');

                alert('Informasi: Pendaftar di bawah umur 17 tahun. Input NIK dan No HP menjadi opsional (tidak wajib), dan status diarahkan ke Remaja Masjid.');
            } else {
                // Dewasa (17 ke atas): NIK & HP Wajib
                $('#nik_input, #no_hp').attr('required', 'required');
                $('.req-star').show();
            }
        });

        // --- 3. SHOW/HIDE DATA MASJID ---
        $('#jenis_anggota').change(function() {
            if ($(this).val() === 'Remaja Mesjid') {
                $('#masjid_container').slideDown();
            } else {
                $('#masjid_container').slideUp();
            }
        });

        // --- 4. FUNGSI AJAX GLOBAL UNTUK DROPDOWN WILAYAH ---
        function refreshSelect2($el) {
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.trigger('change.select2');
            } else {
                $el.trigger('change');
            }
        }

        function loadWilayah(url, paramData, targetDropdown, defaultText) {
            targetDropdown.html('<option value="">Memuat...</option>');
            if(paramData) {
                $.ajax({
                    url: url,
                    data: paramData,
                    success: function(data) {
                        targetDropdown.html('<option value="">-- ' + defaultText + ' --</option>');
                        data.forEach(item => {
                            targetDropdown.append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                        refreshSelect2(targetDropdown);
                    }
                });
            } else {
                targetDropdown.html('<option value="">-- ' + defaultText + ' --</option>');
                refreshSelect2(targetDropdown);
            }
        }

        // --- 5. EVENT LISTENER WILAYAH KTP ---
        $('#ktp_provinsi').change(function() {
            loadWilayah("/admin/get-cities", {provinsi_id: $(this).val()}, $('#ktp_kota'), "Pilih Kota/Kabupaten");
            $('#ktp_kecamatan').html('<option value="">-- Pilih Kota Dulu --</option>'); // Reset anak
        });
        $('#ktp_kota').change(function() {
            loadWilayah("/admin/get-districts", {kota_id: $(this).val()}, $('#ktp_kecamatan'), "Pilih Kecamatan");
        });

        // --- 6. EVENT LISTENER WILAYAH DOMISILI ---
        $('#domisili_provinsi').change(function() {
            loadWilayah("/admin/get-cities", {provinsi_id: $(this).val()}, $('#domisili_kota'), "Pilih Kota/Kabupaten");
            $('#domisili_kecamatan').html('<option value="">-- Pilih Kota Dulu --</option>');
        });
        $('#domisili_kota').change(function() {
            loadWilayah("/admin/get-districts", {kota_id: $(this).val()}, $('#domisili_kecamatan'), "Pilih Kecamatan");
        });

        // --- 7. KEAJAIBAN COPY "SAMA DENGAN KTP" ---
        $('#sama_dengan_ktp').change(function() {
            if($(this).is(':checked')) {
                // Copy isi dropdown HTML dan Value-nya
                $('#domisili_provinsi').html($('#ktp_provinsi').html()).val($('#ktp_provinsi').val());
                $('#domisili_kota').html($('#ktp_kota').html()).val($('#ktp_kota').val());
                $('#domisili_kecamatan').html($('#ktp_kecamatan').html()).val($('#ktp_kecamatan').val());
                refreshSelect2($('#domisili_provinsi'));
                refreshSelect2($('#domisili_kota'));
                refreshSelect2($('#domisili_kecamatan'));

                // Copy teks alamat detail
                $('#alamat_domisili').val($('#alamat_ktp').val());

                // Kunci area domisili secara visual agar tidak diedit manual
                $('#blok_domisili').css({'pointer-events': 'none', 'opacity': '0.6'});
                refreshSelect2($('#domisili_provinsi'));
                refreshSelect2($('#domisili_kota'));
                refreshSelect2($('#domisili_kecamatan'));
            } else {
                // Lepas kunci dan reset domisili
                $('#blok_domisili').css({'pointer-events': 'auto', 'opacity': '1'});
                $('#domisili_provinsi').val('').trigger('change.select2');
                // $('#domisili_provinsi').val('');
                $('#domisili_kota').html('<option value="">-- Pilih Provinsi Dulu --</option>');
                $('#domisili_kecamatan').html('<option value="">-- Pilih Kota Dulu --</option>');
                refreshSelect2($('#domisili_provinsi'));
                refreshSelect2($('#domisili_kota'));
                refreshSelect2($('#domisili_kecamatan'));
                $('#alamat_domisili').val('');
            }
        });

        // Jika teks KTP diubah manual saat checkbox masih dicentang, update domisili secara real-time
        $('#alamat_ktp').on('keyup', function() {
            if($('#sama_dengan_ktp').is(':checked')) {
                $('#alamat_domisili').val($(this).val());
            }
        });

        // Trigger kondisi awal jika ada error validasi saat reload
        if ($('#jenis_anggota').val() === 'Remaja Mesjid') {
            $('#masjid_container').show();
        }

    });
</script>
@endpush
