@extends('layouts.admin')

@section('title', 'Manajemen Data Anggota')
@section('page_heading', 'Manajemen Data Anggota')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Custom Styling agar mirip dengan template referensi */
    .table > thead { background-color: #f8f9fa !important; }
    .table > thead > tr > th { font-weight: 700; color: #333; text-transform: capitalize; border-bottom: 2px solid #dee2e6; padding: 12px; }
    .table > tbody > tr > td { vertical-align: middle; padding: 12px; }
    .dt-search { display: none; } /* Sembunyikan search bawaan karena kita pindahkan custom ke tempat lain jika diperlukan, atau atur via dom */
</style>
@endpush

@section('content')

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center">

            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark mb-1">Filter Provinsi</label>
                <select id="filter_provinsi" class="form-select form-select-sm text-secondary">
                    <option value="">Semua Provinsi</option>
                    @if(auth()->user()->role == 'Admin PP')
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark mb-1">Jenis Anggota</label>
                <select id="filter_jenis" class="form-select form-select-sm text-secondary">
                    <option value="">Semua Jenis</option>
                    <option value="Pengurus">Pengurus</option>
                    <option value="Remaja Mesjid">Remaja Masjid</option>
                    <option value="Alumni">Biasa/Alumni</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-dark mb-1">Status Verifikasi</label>
                <select id="filter_status" class="form-select form-select-sm text-secondary">
                    <option value="">Semua Status</option>
                    <option value="Verified">Verified</option>
                    <option value="Pending">Pending</option>
                    <option value="Pending Update">Pending Update</option>
                </select>
            </div>

            <div class="col-md-3 text-end mt-4">
                <button type="button" id="btn-export" class="btn btn-success btn-sm fw-bold px-3">
                    <i class="fas fa-file-excel me-1"></i> Export
                </button>
                <a href="{{ route('anggota.create') }}" class="btn btn-primary btn-sm fw-bold px-3 ms-1">
                    <i class="fas fa-plus me-1"></i> Tambah Anggota
                </a>
            </div>

        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-table me-2"></i>Daftar Seluruh Anggota JPRMI</h6>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="tableAnggota" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No Urut</th>
                        <th>ID Anggota</th>
                        <th>Nama Lengkap</th>
                        <th>Wilayah (Prov/Kota)</th>
                        <th>Jenis Anggota</th>
                        <th>Masjid Aktif</th>
                        <th>Status</th>
                        <th width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {

        let table = $('#tableAnggota').DataTable({
            processing: true,
            serverSide: true,
            // Memindahkan input search bawaan ke kanan atas tabel (DOM styling DataTables)
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            ajax: {
                url: "{{ route('anggota.data') }}",
                data: function (d) {
                    d.provinsi_id = $('#filter_provinsi').val();
                    d.jenis_anggota = $('#filter_jenis').val();
                    d.status_verifikasi = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'no_urut', name: 'nia_urut', orderable: true, searchable: false},
                {data: 'nia', name: 'nia', orderable: true},
                {data: 'nama_lengkap', name: 'nama_lengkap'},
                {data: 'wilayah', name: 'wilayah', searchable: false},
                {data: 'jenis_anggota', name: 'jenis_anggota'},
                {data: 'masjid_aktif', name: 'masjid_aktif'},
                {data: 'status_verifikasi', name: 'status_verifikasi'},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false},
            ],
            order: [[0, 'asc']],
            language: {
                search: "", // Menghilangkan teks "Search:"
                searchPlaceholder: "Cari Nama / NIK...",
                lengthMenu: "_MENU_ Data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ anggota",
                paginate: { first: "Awal", last: "Akhir", next: "Selanjutnya", previous: "Sebelumnya" }
            }
        });

        // Trigger filter otomatis saat dropdown diubah
        $('#filter_provinsi, #filter_jenis, #filter_status').change(function() {
            table.draw();
        });

        // Script Export Excel Berdasarkan Filter
        $('#btn-export').click(function() {
            let prov = $('#filter_provinsi').val() || '';
            window.location.href = "{{ route('anggota.exportKta') }}?provinsi_id=" + prov;
        });

    });
</script>
@endpush
