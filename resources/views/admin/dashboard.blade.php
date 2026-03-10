@extends('layouts.admin')

@section('title', 'Dashboard Statistik - JPRMI')
@section('page_heading', 'Dashboard Statistik')

@push('styles')
<style>
    .card-stat { transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; }
    .card-stat:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .icon-bg { position: absolute; right: -10px; bottom: -20px; font-size: 6rem; opacity: 0.1; transform: rotate(-15deg); }
</style>
@endpush

@section('content')
<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card card-stat border-0 shadow-sm border-start border-success border-4 h-100 position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="text-muted small text-uppercase fw-bold mb-1">Total Anggota Terverifikasi</div>
                <h2 class="mb-0 fw-bolder text-success">{{ number_format($totalVerified, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Orang</span></h2>
                <i class="fas fa-users icon-bg text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card card-stat border-0 shadow-sm border-start border-danger border-4 h-100 position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="text-muted small text-uppercase fw-bold mb-1">Menunggu Verifikasi (Baru)</div>
                <h2 class="mb-0 fw-bolder text-danger">{{ number_format($totalPending, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Antrean</span></h2>
                <i class="fas fa-user-clock icon-bg text-danger"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12">
        <div class="card card-stat border-0 shadow-sm border-start border-warning border-4 h-100 position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="text-muted small text-uppercase fw-bold mb-1">Ajuan Perubahan Data (Update)</div>
                <h2 class="mb-0 fw-bolder text-warning">{{ number_format($totalDrafts, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Draft</span></h2>
                <i class="fas fa-edit icon-bg text-warning"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-success"><i class="fas fa-chart-line me-2"></i>Pertumbuhan Anggota</h6></div>
            <div class="card-body"><canvas id="pertumbuhanChart" height="80"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-success"><i class="fas fa-venus-mars me-2"></i>Jenis Kelamin</h6></div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div style="width: 80%;"><canvas id="genderChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-success"><i class="fas fa-birthday-cake me-2"></i>Sebaran Usia Anggota</h6></div>
            <div class="card-body"><canvas id="ageChart"></canvas></div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="m-0 fw-bold text-success"><i class="fas fa-map-marked-alt me-2"></i>Top 10 Sebaran Provinsi</h6></div>
            <div class="card-body"><canvas id="provinceChart"></canvas></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {

        // --- 1. GRAFIK PERTUMBUHAN (Line Chart) ---
        const ctxPertumbuhan = $('#pertumbuhanChart')[0].getContext('2d');
        let gradientPertumbuhan = ctxPertumbuhan.createLinearGradient(0, 0, 0, 400);
        gradientPertumbuhan.addColorStop(0, 'rgba(25, 135, 84, 0.5)');
        gradientPertumbuhan.addColorStop(1, 'rgba(25, 135, 84, 0.0)');

        new Chart(ctxPertumbuhan, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelsTahun) !!},
                datasets: [{
                    label: 'Anggota Bergabung',
                    data: {!! json_encode($dataPertumbuhan) !!},
                    borderColor: '#198754', backgroundColor: gradientPertumbuhan,
                    borderWidth: 3, fill: true, tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // --- 2. GRAFIK GENDER (Doughnut Chart) ---
        new Chart($('#genderChart')[0], {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $totalLaki }}, {{ $totalPerempuan }}],
                    backgroundColor: ['#0dcaf0', '#e83e8c'], // Cyan untuk Laki, Pink untuk Perempuan
                    borderWidth: 2, hoverOffset: 5
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // --- 3. GRAFIK UMUR (Bar Chart) ---
        new Chart($('#ageChart')[0], {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($ageBrackets)) !!},
                datasets: [{
                    label: 'Jumlah Orang',
                    data: {!! json_encode(array_values($ageBrackets)) !!},
                    backgroundColor: '#ffc107', borderRadius: 5
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // --- 4. GRAFIK PROVINSI (Horizontal Bar Chart) ---
        new Chart($('#provinceChart')[0], {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsProvinsi) !!},
                datasets: [{
                    label: 'Jumlah Anggota',
                    data: {!! json_encode($dataProvinsi) !!},
                    backgroundColor: '#198754', borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y', // Mengubah grafik menjadi Horizontal
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } }
            }
        });

    });
</script>
@endpush
