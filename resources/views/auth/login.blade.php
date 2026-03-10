<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Jaringan Pemuda Remaja Masjid Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('images/logo-jprmi.png')}}" type="image/x-icon">

    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #0f5132, #198754);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header h4 {
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .btn-login {
            background-color: #0f5132;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
        }
        .btn-login:hover {
            background-color: #0c4128;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card login-card">
                <div class="login-header">
                    <div class="mb-3">
                        <img src="{{ asset('images/logo-jprmi.png') }}" alt="Logo JPRMI" class="rounded" width="150" height="150">
                    </div>
                    <h4>Jaringan Pemuda Remaja Masjid Indonesia</h4>
                    <small class="opacity-75">Manajemen Anggota JPRMI</small>
                </div>

                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger pb-0 rounded-3">
                            <ul class="small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@jprmi.or.id" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-login shadow-sm">
                                <i class="fas fa-sign-in-alt me-2"></i> LOGIN MASUK
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">Jaringan Pemuda Remaja Masjid Indonesia &copy; {{ date('Y') }}</small>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
