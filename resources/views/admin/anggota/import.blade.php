@extends('layouts.admin')

@section('title', 'Import Data Anggota - JPRMI')
@section('page_heading', 'Import CSV Data Anggota Lama')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-success"><i class="fas fa-file-excel me-2"></i>Upload File Data Anggota</h6>
            </div>
            <div class="card-body p-4">

                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Panduan Import:</h6>
                    <ul class="mb-0 small">
                        <li>Pastikan file berformat <strong>.CSV</strong> atau <strong>.XLSX</strong>.</li>
                        <li>Pastikan baris pertama adalah <strong>Header (Nama Kolom)</strong>.</li>
                        <li>Sistem akan otomatis mencocokkan kolom "Provinsi" dan "Kota/Kabupaten" ke database Master Wilayah.</li>
                        <li>Sistem akan mempertahankan nomor urut berdasarkan data yang ada di file Excel Anda dan men-generate nomor NIA format baru (PP.KK.YY.UUUUUU).</li>
                    </ul>
                </div>

                <form action="{{ route('anggota.importData') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="file_csv" class="form-label fw-bold">Pilih File CSV/Excel</label>
                        <input class="form-control form-control-lg" type="file" id="file_csv" name="file_csv" accept=".csv, .xlsx, .xls" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('anggota.index') }}" class="btn btn-light border"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-cloud-upload-alt me-2"></i> Mulai Proses Import</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
