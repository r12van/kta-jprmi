<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin JPRMI')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f6f9; overflow-x: hidden; }

        /* Sidebar Styling */
        #wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f5132 0%, #198754 100%);
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar.toggled { margin-left: -250px; }

        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h4 { margin: 0; font-weight: 800; letter-spacing: 1px; }

        .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; transition: all 0.3s; border-radius: 0 25px 25px 0; margin-right: 15px; margin-top: 5px; }
        .nav-link:hover, .nav-link.active { background-color: #198754; color: #ffc107; font-weight: bold; }
        .nav-link i { width: 25px; }

        /* Page Content */
        #page-content-wrapper { width: 100%; transition: all 0.3s; }
        .navbar-light { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        @media (max-width: 768px) {
            #sidebar { margin-left: -250px; }
            #sidebar.toggled { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4>SIM-JPRMI</h4>
            <small class="opacity-75">Sistem Manajemen</small>
        </div>

        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}" href="{{ route('anggota.index') }}">
                    <i class="fas fa-users"></i> Data Anggota
                </a>
            </li>

            @if(in_array(auth()->user()->role, ['Admin PW', 'Admin PP']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.index') }}">
                    <i class="fas fa-clipboard-check"></i> Persetujuan Data
                </a>
            </li>
            @endif

            @if(in_array(auth()->user()->role, ['Admin PW', 'Admin PP']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-users-cog"></i> Manajemen Akun
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <div id="page-content-wrapper">

        <nav class="navbar navbar-expand-lg navbar-light py-3 px-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-success border-0 me-3" id="menu-toggle"><i class="fas fa-bars fs-5"></i></button>
                <h5 class="mb-0 fw-bold text-success">@yield('page_heading', 'Dashboard')</h5>
            </div>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end me-2 d-none d-md-block">
                            <div class="fw-bold small">{{ auth()->user()->nama ?? 'Admin' }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ auth()->user()->role ?? 'Role' }}</div>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama ?? 'A') }}&background=0f5132&color=fff" class="rounded-circle" width="35" height="35" alt="Avatar">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                                <i class="fas fa-user-circle me-2 text-muted"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function () {
        function initSelect2(scope) {
            $(scope).find('select').each(function () {
                const $select = $(this);

                if ($select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                const $modal = $select.closest('.modal');
                const config = {
                    theme: 'bootstrap-5',
                    width: '100%',
                };

                if ($modal.length) {
                    config.dropdownParent = $modal;
                }

                $select.select2(config);
            });
        }

        initSelect2(document);

        $('#menu-toggle').on('click', function (e) {
            e.preventDefault();
            $('#sidebar').toggleClass('toggled');
        });

        $(document).on('shown.bs.modal', '.modal', function () {
            initSelect2(this);
        });

        $(document).on('draw.dt', function () {
            initSelect2(document);
        });
    });
</script>

@stack('scripts')

</body>
</html>
