@extends('layouts.admin')

@section('title', 'Detail Anggota - JPRMI')
@section('page_heading', 'Profil Anggota')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #004d00 0%, #006400 100%);
        height: 150px;
        border-radius: 10px 10px 0 0;
        position: relative;
    }
    .profile-img-container {
        position: absolute;
        top: -60px;
        left: 30px;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        object-fit: cover;
        background-color: #ddd;
    }
    .profile-actions {
        position: absolute;
        bottom: 15px;
        right: 20px;
    }
    .data-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: bold;
        margin-bottom: 2px;
    }
    .data-value {
        font-weight: 600;
        font-size: 1.05rem;
        margin-bottom: 15px;
        color: #333;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">

        <div class="card shadow-sm border-0 mb-4">
            <div class="profile-header">
                <div class="profile-actions d-flex gap-2">
                    @if($anggota->status_verifikasi == 'Verified')
                        <a href="{{ route('anggota.print', $anggota->id) }}" target="_blank" class="btn btn-warning btn-sm fw-bold"><i class="fas fa-id-card me-1"></i> Cetak KTA</a>
                    @endif
                    <a href="{{ route('anggota.edit', $anggota->id) }}" class="btn btn-light btn-sm fw-bold text-success"><i class="fas fa-edit me-1"></i> Edit Data</a>
                </div>
            </div>
            <div class="card-body pt-0 pb-4">
                <div class="row">
                    <div class="col-lg-2 col-md-3 position-relative">
                        <div class="profile-img-container">
                            @if($anggota->foto_diri == 'default-avatar.png')
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($anggota->nama_lengkap) }}&background=004d00&color=fff&size=200" class="profile-img shadow-sm">
                            @else
                                <img src="{{ asset('storage/' . $anggota->foto_diri) }}" class="profile-img shadow-sm">
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-9 pt-3 ps-md-4 mt-5 mt-md-0">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <h3 class="fw-bold mb-1">{{ $anggota->nama_lengkap }}</h3>
                                <div class="mb-2">
                                    @if($anggota->jenis_anggota == 'Pengurus')
                                        <span class="badge bg-warning text-dark me-1">PENGURUS</span>
                                    @elseif($anggota->jenis_anggota == 'Remaja Mesjid')
                                        <span class="badge bg-info text-dark me-1">REMAJA MESJID</span>
                                    @else
                                        <span class="badge bg-secondary me-1">ALUMNI</span>
                                    @endif

                                    @if($anggota->status_verifikasi == 'Verified')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> VERIFIED</span>
                                    @elseif($anggota->status_verifikasi == 'Pending Update')
                                        <span class="badge bg-primary"><i class="fas fa-clock me-1"></i> MENUNGGU UPDATE</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> PENDING</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $anggota->kota->name ?? '-' }}, {{ $anggota->provinsi->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="border rounded p-2 bg-light d-inline-block text-start" style="min-width: 200px;">
                                    <small class="text-muted d-block fw-bold">Nomor Induk Anggota (NIA)</small>
                                    <div class="fw-bold fs-5 text-success">{{ $anggota->nia ?? 'Belum ada NIA' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold text-success mb-0"><i class="fas fa-user-circle me-2"></i> Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                <hr class="mt-2 mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="data-label">Nomor Induk Kependudukan (NIK)</div>
                        <div class="data-value">{{ $anggota->nik ?? '-' }}</div>

                        <div class="data-label">Tempat, Tanggal Lahir</div>
                        <div class="data-value">
                            {{ $anggota->tempat_lahir ?? '-' }},
                            {{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                        </div>

                        <div class="data-label">Jenis Kelamin</div>
                        <div class="data-value">{{ $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : ($anggota->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="data-label">Nomor WhatsApp / HP</div>
                        <div class="data-value text-success"><i class="fab fa-whatsapp me-1"></i> {{ $anggota->no_hp ?? '-' }}</div>

                        <div class="data-label">Tahun Bergabung</div>
                        <div class="data-value">{{ $anggota->tahun_masuk }}</div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="data-label">Alamat Lengkap (Sesuai Domisili)</div>
                        <div class="data-value bg-light p-3 rounded border">{{ $anggota->alamat_lengkap ?? 'Alamat belum diisi lengkap.' }}</div>
                    </div>
                    @if($anggota->jenis_anggota == 'Remaja Mesjid' && $anggota->nama_masjid)
                    <div class="col-12 mt-3">
                        <div class="p-3 bg-info bg-opacity-10 rounded border border-info border-opacity-25">
                            <h6 class="fw-bold text-info mb-2"><i class="fas fa-mosque me-2"></i>Informasi Masjid Asal</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="data-label">Nama Masjid</div>
                                    <div class="fw-bold text-dark">{{ $anggota->nama_masjid }}</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="data-label">Alamat Masjid</div>
                                    <div class="fw-bold text-dark">{{ $anggota->alamat_masjid ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fas fa-history me-2 text-muted"></i> Riwayat Data
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Diinput Oleh:</span>
                        <span class="fw-bold text-end">{{ $anggota->createdBy->nama ?? 'Sistem Import' }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Tanggal Daftar:</span>
                        <span class="fw-bold">{{ $anggota->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Terakhir Update:</span>
                        <span class="fw-bold">{{ $anggota->updated_at->translatedFormat('d M Y, H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fas fa-id-card-alt me-2 text-muted"></i> Dokumen KTP
            </div>
            <div class="card-body text-center p-4">
                @if($anggota->foto_ktp)
                    <div class="mb-2 text-success"><i class="fas fa-check-circle me-1"></i> KTP Terlampir</div>
                    <button type="button" class="btn btn-sm btn-outline-primary w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#ktpModal">
                        <i class="fas fa-id-card me-1"></i> Lihat Dokumen KTP
                    </button>
                @else
                    <div class="text-muted small mb-2"><i class="fas fa-times-circle me-1"></i> KTP Belum Dilampirkan</div>
                    <span class="badge bg-light text-dark border">Tidak ada dokumen</span>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-top border-warning border-3">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-sitemap me-2 text-warning"></i> Struktural JPRMI</span>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalPengurus">
                    <i class="fas fa-plus"></i> Set Jabatan
                </button>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($anggota->kepengurusan as $jabatan)
                    <li class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">
                                    {{ $jabatan->jabatan }}

                                    @if($jabatan->nama_bidang)
                                        @php
                                            // Menentukan Ikon Berdasarkan Nama Bidang (PHP 8 Match)
                                            $iconBidang = match($jabatan->nama_bidang) {
                                                'Bidang Pemberdayaan' => 'fas fa-seedling text-success',
                                                'Bidang Pengembangan Organisasi dan Jaringan Antar Lembaga' => 'fas fa-network-wired text-primary',
                                                'Bidang Sumber Daya Insani (Kaderisasi)' => 'fas fa-users-cog text-info',
                                                'Bidang Hukum dan Advokasi' => 'fas fa-balance-scale text-danger',
                                                'Bidang Humas dan Media' => 'fas fa-bullhorn text-warning',
                                                'Deputi Taruna Siaga Masjid' => 'fas fa-shield-alt text-dark',
                                                'Deputi Komunitas Remaja Muslimah' => 'fas fa-female text-pink',
                                                'Deputi Sekolah Berkarakter Mulia' => 'fas fa-school text-secondary',
                                                default => 'fas fa-layer-group text-primary', // Untuk pilihan 'Other' / Lainnya
                                            };
                                        @endphp

                                        <span class="d-block small mt-1 text-muted">
                                            <i class="{{ $iconBidang }} me-1"></i> {{ $jabatan->nama_bidang }}
                                        </span>
                                    @endif
                                </h6>
                            </div>

                            <div class="text-end">
                                @if($jabatan->is_active)
                                    <span class="badge bg-success mb-2 d-inline-block"><i class="fas fa-check-circle"></i> Aktif</span>

                                    <form action="{{ route('anggota.nonaktifkanPengurus', $jabatan->id) }}" method="POST" onsubmit="return confirm('Akhiri masa jabatan ini (Demisioner)?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">
                                            <i class="fas fa-power-off me-1"></i> Selesai
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-history"></i> Demisioner</span>
                                @endif
                            </div>

                        </div>
                    </li>
                    @empty
                    <li class="list-group-item p-4 text-center text-muted small">
                        Anggota ini belum memiliki riwayat jabatan struktural.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card shadow-sm border-top border-danger border-3">
            <div class="card-body">
                <p class="small text-muted fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Zona Bahaya</p>
                <div class="d-grid gap-2">
                    <form action="{{ route('anggota.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anggota ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-trash me-1"></i> Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="modalPengurus" tabindex="-1" aria-labelledby="modalPengurusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold" id="modalPengurusLabel"><i class="fas fa-user-tie me-2"></i> Angkat Menjadi Pengurus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('anggota.storePengurus', $anggota->id) }}" method="POST" id="formSetJabatan">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tingkat Kepengurusan <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Pengurus Pusat (PP)">Pengurus Pusat (PP)</option>
                            <option value="Pengurus Wilayah (PW)">Pengurus Wilayah (PW)</option>
                            <option value="Pengurus Daerah (PD)">Pengurus Daerah (PD)</option>
                            <option value="Pengurus Cabang (PC)">Pengurus Cabang (PC)</option>
                            <option value="Pengurus Ranting (PR)">Pengurus Ranting (PR)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Posisi Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="Ketua">Ketua</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Bendahara">Bendahara</option>
                            <option value="Ketua Bidang">Ketua Bidang</option>
                            <option value="Sekretaris Bidang">Sekretaris Bidang</option>
                            <option value="Kepala Departemen">Kepala Departemen</option>
                            <option value="Anggota">Anggota</option>
                            <option value="Anggota Masjid">Anggota Masjid</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Bidang / Departemen <span class="text-muted fw-normal">(Opsional)</span></label>
                        <select name="nama_bidang" id="select_bidang" class="form-select">
                            <option value="">-- Tidak Ada / Kosongkan --</option>
                            <option value="Bidang Pemberdayaan">Bidang Pemberdayaan</option>
                            <option value="Bidang Pengembangan Organisasi dan Jaringan Antar Lembaga">Bidang Pengembangan Org. & Jaringan</option>
                            <option value="Bidang Sumber Daya Insani (Kaderisasi)">Bidang Sumber Daya Insani (Kaderisasi)</option>
                            <option value="Bidang Hukum dan Advokasi">Bidang Hukum dan Advokasi</option>
                            <option value="Bidang Humas dan Media">Bidang Humas dan Media</option>
                            <option value="Deputi Taruna Siaga Masjid">Deputi Taruna Siaga Masjid</option>
                            <option value="Deputi Komunitas Remaja Muslimah">Deputi Komunitas Remaja Muslimah</option>
                            <option value="Deputi Sekolah Berkarakter Mulia">Deputi Sekolah Berkarakter Mulia</option>
                            <option value="Other">Lainnya...</option>
                        </select>
                    </div>

                    <div class="mb-3" id="bidang_lainnya_container" style="display: none;">
                        <input type="text" name="bidang_lainnya" class="form-control" placeholder="Ketik nama bidang / departemen lainnya...">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Periode Awal</label>
                            <input type="number" name="periode_awal" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Periode Akhir</label>
                            <input type="number" name="periode_akhir" class="form-control" value="{{ date('Y') + 2 }}" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                        <label class="form-check-label fw-bold text-success" for="isActive">Jadikan Jabatan Aktif Saat Ini</label>
                        <div class="small text-muted">Akan menonaktifkan jabatan sebelumnya dan mengubah status keanggotaan menjadi "Pengurus".</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="btnSimpanJabatan"><i class="fas fa-save me-1"></i> Simpan Jabatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if($anggota->foto_ktp)
<div class="modal fade" id="ktpModal" tabindex="-1" aria-labelledby="ktpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="ktpModalLabel"><i class="fas fa-id-card me-2"></i> Dokumen KTP - {{ $anggota->nama_lengkap }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light p-4" style="overflow: hidden; user-select: none;">
                <div class="position-relative d-inline-block">

                    <img src="{{ asset('storage/' . $anggota->foto_ktp) }}" alt="KTP {{ $anggota->nama_lengkap }}" class="img-fluid rounded shadow-sm" style="max-height: 70vh; object-fit: contain; pointer-events: none;" oncontextmenu="return false;">

                    {{-- <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="pointer-events: none; background: rgba(255,255,255,0.1);">
                        <div style="transform: rotate(-35deg); font-size: clamp(1.5rem, 4vw, 3.5rem); color: rgba(255, 0, 0, 0.4); font-weight: 900; letter-spacing: 5px; text-transform: uppercase; border: 5px solid rgba(255, 0, 0, 0.4); padding: 20px 40px; border-radius: 15px; text-align: center; line-height: 1.2; text-shadow: 2px 2px 4px rgba(255,255,255,0.6);">
                            HANYA UNTUK<br>DATA JPRMI
                        </div>
                    </div> --}}

                </div>
            </div>
            <div class="modal-footer bg-white">
                <a href="{{ asset('storage/' . $anggota->foto_ktp) }}" download="KTP_{{ $anggota->nik }}_{{ str_replace(' ', '_', $anggota->nama_lengkap) }}.jpg" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Unduh KTP
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup Preview</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@push('scripts')
<script>
    $(function () {
        $('#formSetJabatan').on('submit', function () {
            const $submitButton = $('#btnSimpanJabatan');
            if (!$submitButton.length) return;

            $submitButton.prop('disabled', true);
            $submitButton.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
        });
    });
        $('#select_bidang').on('change', function() {
            if ($(this).val() === 'Other') {
                $('#bidang_lainnya_container').show();
            } else {
                $('#bidang_lainnya_container').hide();
                $('#bidang_lainnya_container input').val('');
            }
        });
</script>
@endpush
