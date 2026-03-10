<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak KTA - {{ $anggota->nama_lengkap }}</title>
    <style>
        /* Reset & Setting Print Browser */
        body {
            font-family: 'Arial', sans-serif;
            background: #525659;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        @media print {
            body { background: white; padding: 0; }
            /* Memaksa browser mencetak gambar background */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            @page { margin: 0; size: auto; }
        }

        /* Ukuran Kartu Standar CR80 (Landscape) */
        .kta-card {
            width: 85.6mm;
            height: 53.98mm;
            /* Memanggil gambar template dari folder public/images/ */
            background-image: url('{{ asset("images/template_kta_jprmi.jpg") }}');
            background-size: cover;
            background-position: center;
            position: relative;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            overflow: hidden;
            /* Jika background gelap, ubah warna teks di bawah menjadi #fff (putih) */
            color: #000;
        }

        /* --- KORDINAT TEKS ---
           Ubah nilai 'top' (Atas-Bawah) dan 'left' (Kiri-Kanan)
           untuk mengepaskan posisi teks dengan kotak di gambar template Anda!
        */
        .data-foto {
            position: absolute;
            top: 5mm; /* Geser ke atas/bawah */
            left: 7mm; /* Geser ke kiri/kanan */
        }

        .data-nia {
            position: absolute;
            top: 24mm;  /* Geser ke atas/bawah */
            left: 7mm; /* Geser ke kiri/kanan */
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .data-nama {
            position: absolute;
            top: 30mm;
            left: 7mm;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            width: 50mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .data-alamat {
            position: absolute;
            top: 36mm;
            left: 7mm;
            font-size: 8px;
            font-weight: 600;
            line-height: 1.2;
            width: 40mm; /* Batas lebar alamat agar tidak keluar kartu */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Panel Tombol Bantuan */
        .action-panel {
            position: fixed;
            top: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 100;
        }
        .btn {
            padding: 8px 15px; margin: 0 5px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 12px;
        }
        .btn-print { background: #0f5132; color: white; }
        .btn-close { background: #e0e0e0; color: #333; }
    </style>
</head>
<body>

    <div class="action-panel no-print">
        <div style="margin-bottom: 10px; font-size: 14px; font-weight: bold; color: #333;">Preview KTA Digital</div>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
        <button onclick="window.print()" class="btn btn-print">Cetak KTA</button>
    </div>

    <div class="kta-card">
        <div class="data-foto">
            @if($anggota->foto_diri == 'default-avatar.png')
                <img src="https://ui-avatars.com/api/?name={{ urlencode($anggota->nama_lengkap) }}&background=004d00&color=fff&size=200" class="profile-img shadow-sm" style="width: 15mm; height: 15mm; border-radius: 5%; object-fit: cover;">
            @else
                <img src="{{ asset('storage/' . $anggota->foto_diri) }}" class="profile-img shadow-sm" style="width: 15mm; height: 15mm; border-radius: 5%; object-fit: cover;">
            @endif
        </div>
        <div class="data-nia">{{ $anggota->nia }}</div>
        <div class="data-nama">{{ $anggota->nama_lengkap }}</div>
        <div class="data-alamat">{{ $anggota->alamat_lengkap }}</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(function () {
            // Otomatis print saat halaman dibuka
            setTimeout(function () {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
