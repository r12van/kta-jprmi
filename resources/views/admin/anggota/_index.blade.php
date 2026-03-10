@extends('layouts.admin')

@section('title', 'Data Anggota - JPRMI')
@section('page_heading', 'Manajemen Data Anggota')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 fw-bold text-success mb-2 mb-md-0"><i class="fas fa-users me-2"></i>Daftar Anggota</h6>
        <div>
            <button type="button" id="btn-export" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-print me-1"></i> Export Data KTA
            </button>
            <a href="{{ route('anggota.importForm') }}" class="btn btn-outline-success btn-sm me-2"><i class="fas fa-file-upload me-1"></i> Import CSV</a>
            <a href="{{ route('anggota.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus me-1"></i> Tambah Anggota</a>
        </div>
    </div>

    <div class="card-body">

        @if(auth()->user()->role == 'Admin PP')
        <div class="row mb-4 bg-light p-3 rounded border">
            <div class="col-md-5">
                <label class="form-label fw-bold small">Filter Provinsi</label>
                <select id="filter_provinsi" class="form-select form-select-sm">
                    <option value="">-- Semua Provinsi --</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-bold small">Filter Kota/Kabupaten</label>
                <select id="filter_kota" class="form-select form-select-sm">
                    <option value="">-- Semua Kota --</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button id="btn-filter" class="btn btn-success btn-sm w-100"><i class="fas fa-filter"></i> Terapkan</button>
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table id="tableAnggota" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIA</th>
                        <th>Nama Lengkap</th>
                        <th>Wilayah</th>
                        <th>Jenis Anggota</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
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
        // Inisialisasi DataTables Server-Side
        let table = $('#tableAnggota').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('anggota.data') }}",
                data: function (d) {
                    d.provinsi_id = $('#filter_provinsi').val();
                    d.kota_id = $('#filter_kota').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nia', name: 'nia', render: function(data) { return '<span class="fw-bold text-success">'+(data ? data : 'Pending')+'</span>'; }},
                {data: 'nama_lengkap', name: 'nama_lengkap'},
                {data: 'wilayah', name: 'wilayah', searchable: false},
                {data: 'jenis_anggota', name: 'jenis_anggota'},
                {data: 'status_verifikasi', name: 'status_verifikasi'},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: {
                search: "Cari Nama / NIA:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari total _TOTAL_ anggota",
                paginate: { first: "Awal", last: "Akhir", next: "Selanjutnya", previous: "Sebelumnya" }
            }
        });

        // Script Filter DataTables
        $('#btn-filter').click(function() {
            table.draw();
        });

        // Script Export Excel Berdasarkan Filter
        $('#btn-export').click(function() {
            let prov = $('#filter_provinsi').val() || '';
            let kota = $('#filter_kota').val() || '';
            window.location.href = "{{ route('anggota.exportKta') }}?provinsi_id=" + prov + "&kota_id=" + kota;
        });

        // AJAX Dropdown Kota untuk Filter
        $('#filter_provinsi').change(function() {
            let provId = $(this).val();
            let kotaSelect = $('#filter_kota');
            kotaSelect.html('<option value="">Memuat data...</option>');

            if(provId) {
                $.getJSON('/admin/get-cities', { provinsi_id: provId }, function(data) {
                    kotaSelect.html('<option value="">-- Semua Kota --</option>');
                    $.each(data, function(_, kota) {
                        kotaSelect.append('<option value="' + kota.id + '">' + kota.name + '</option>');
                    });
                });
            } else {
                kotaSelect.html('<option value="">-- Semua Kota --</option>');
            }
        });
    });
</script>
@endpush
