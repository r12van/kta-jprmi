<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tanda Anggota JPRMI | Jaringan Pemuda Remaja Masjid Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="shortcut icon" href="{{asset('images/logo-jprmi.png')}}" type="image/x-icon">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: 800; color: #198754 !important; letter-spacing: 1px; }
        #map { height: 75vh; width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 1; }
        .hero-section { padding: 40px 0 20px 0; }
        .leaflet-popup-content-wrapper { border-radius: 10px; }
        .leaflet-popup-content { margin: 15px; font-weight: bold; text-align: center; }
        .stat-badge { font-size: 1.5rem; font-weight: 900; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{asset('images/logo-jprmi.png')}}" alt="logo JPRMI" class="me-2" width="40" height="40"> KTA - JPRMI
            </a>
            <div class="d-flex align-items-center">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success fw-bold px-4 rounded-pill">
                        <i class="fas fa-chart-line me-2"></i> Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success fw-bold px-4 rounded-pill">
                        <i class="fas fa-sign-in-alt me-2"></i> Login Pengurus
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container hero-section">
        <div class="row align-items-center mb-4">
            <div class="col-md-8 text-center text-md-start mb-3 mb-md-0">
                <h2 class="fw-bolder text-dark mb-1">Peta Sebaran Keanggotaan Nasional</h2>
                <p class="text-muted mb-0">Jaringan Pemuda Remaja Masjid Indonesia (JPRMI)</p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <div class="bg-success text-white d-inline-block px-4 py-2 rounded-3 shadow-sm">
                    <div class="small text-white-50 text-uppercase fw-bold">Total Nasional</div>
                    <div class="stat-badge">{{ number_format($totalNasional, 0, ',', '.') }} <span class="fs-6 fw-normal">Anggota</span></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // 1. Inisialisasi Peta (Pusat di tengah Indonesia)
        var map = L.map('map').setView([-2.5489, 118.0149], 5);

        // 2. Tambahkan Tile Layer (Desain Terang/Positron dari CartoDB agar terlihat elegan)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CartoDB</a> | KTA JPRMI',
            subdomains: 'abcd',
            maxZoom: 10,
            minZoom: 4
        }).addTo(map);

        // 3. Database Koordinat Dasar Provinsi Indonesia
        const koordinatProvinsi = {
            "ACEH": [4.6951, 96.7494], "SUMATERA UTARA": [2.1154, 99.5451], "SUMATERA BARAT": [-0.7399, 100.8000],
            "RIAU": [0.2933, 101.7068], "JAMBI": [-1.6101, 103.6131], "SUMATERA SELATAN": [-3.3194, 104.9147],
            "BENGKULU": [-3.7928, 102.2608], "LAMPUNG": [-5.0097, 105.1500], "KEPULAUAN BANGKA BELITUNG": [-2.7411, 106.4406],
            "KEPULAUAN RIAU": [3.9456, 108.1429], "DKI JAKARTA": [-6.2088, 106.8456], "JAWA BARAT": [-6.9147, 107.6098],
            "JAWA TENGAH": [-7.1509, 110.1403], "DI YOGYAKARTA": [-7.7956, 110.3695], "JAWA TIMUR": [-7.9666, 112.9508],
            "BANTEN": [-6.4058, 106.0640], "BALI": [-8.4095, 115.1889], "NUSA TENGGARA BARAT": [-8.6529, 117.3616],
            "NUSA TENGGARA TIMUR": [-8.6226, 121.0794], "KALIMANTAN BARAT": [-0.2787, 111.4753], "KALIMANTAN TENGAH": [-1.5373, 113.8191],
            "KALIMANTAN SELATAN": [-3.0926, 115.2838], "KALIMANTAN TIMUR": [0.5387, 116.4194], "KALIMANTAN UTARA": [3.0731, 116.0414],
            "SULAWESI UTARA": [0.6247, 123.9750], "SULAWESI TENGAH": [-1.4300, 121.4456], "SULAWESI SELATAN": [-4.1449, 119.9001],
            "SULAWESI TENGGARA": [-4.1449, 122.1746], "GORONTALO": [0.5435, 123.0568], "SULAWESI BARAT": [-2.8441, 119.2312],
            "MALUKU": [-3.2385, 130.1453], "MALUKU UTARA": [1.5709, 127.8088], "PAPUA BARAT": [-1.3361, 133.1747], "PAPUA": [-4.2699, 138.0804]
        };

        // 4. Data Sebaran dari Backend Laravel
        const dataSebaran = @json($sebaran);

        // 5. Render Lingkaran (CircleMarkers) ke Peta
        dataSebaran.forEach(function(item) {
            let namaProvinsi = item.provinsi.toUpperCase();

            // Cek apakah koordinat provinsi ada di database JS kita
            if (koordinatProvinsi[namaProvinsi]) {
                let latlng = koordinatProvinsi[namaProvinsi];

                // Rumus memperbesar lingkaran berdasarkan jumlah anggota
                let radiusBulat = 10 + (item.total * 0.5);
                if (radiusBulat > 50) radiusBulat = 50; // Max radius

                // Buat Circle Marker Warna Hijau
                L.circleMarker(latlng, {
                    radius: radiusBulat,
                    fillColor: "#198754",
                    color: "#0f5132",
                    weight: 2,
                    opacity: 0.8,
                    fillOpacity: 0.5
                }).addTo(map).bindPopup(
                    `<div class="text-muted small text-uppercase">${namaProvinsi}</div>
                     <div class="fs-4 text-success">${item.total} <span class="fs-6 text-dark">Anggota</span></div>`
                );
            }
        });
    </script>
</body>
</html>
