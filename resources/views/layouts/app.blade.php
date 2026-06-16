<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Prasetya Rent Car')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1a3c5e;
            --secondary-color: #f5a623;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #152f48;
            border-color: #152f48;
        }
        
        .btn-warning {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #d9901f;
            border-color: #d9901f;
            color: white;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
        }
        
        .card {
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,.2);
        }

        /* Custom Red Navbar (Movus style) */
        .custom-navbar-container {
            position: sticky;
            top: 0;
            z-index: 1030;
            padding-top: 15px;
            padding-bottom: 5px;
            background-color: transparent;
        }
        
        .navbar-custom {
            background-color: #e30613 !important;
            border-radius: 50px;
            padding: 10px 24px !important;
            box-shadow: 0 8px 20px rgba(227, 6, 19, 0.15);
            transition: all 0.3s ease;
        }
        
        .navbar-custom .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-custom .navbar-brand i {
            color: #ffffff !important;
        }
        
        .navbar-custom .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 8px 16px !important;
            border-radius: 30px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .navbar-custom .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }
        
        .navbar-custom .nav-item .active {
            background-color: rgba(0, 0, 0, 0.2) !important;
            font-weight: 600;
        }
        
        /* White Pill Button for 'Daftar' */
        .navbar-custom .btn-daftar-pill {
            background-color: #ffffff !important;
            color: #e30613 !important;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            padding: 8px 24px !important;
            border-radius: 30px;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }
        
        .navbar-custom .btn-daftar-pill:hover {
            background-color: #f8f9fa !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            color: #e30613 !important;
        }
        
        /* Dropdown custom styling */
        .navbar-custom .dropdown-menu {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 10px;
            padding: 8px;
        }
        
        .navbar-custom .dropdown-item {
            border-radius: 10px;
            padding: 8px 16px;
            color: #333;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .navbar-custom .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #e30613;
        }
        
        .navbar-custom .dropdown-item.text-danger:hover {
            background-color: #fff5f5;
            color: #dc3545 !important;
        }

        /* Hamburger button icon */
        .navbar-custom .navbar-toggler {
            border: none;
            padding: 5px;
        }
        .navbar-custom .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-custom .navbar-toggler-icon {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <div class="container custom-navbar-container">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="bi bi-car-front-fill"></i> Prasetya Rent Car
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cars.*') ? 'active' : '' }}" href="{{ route('cars.index') }}">Katalog Mobil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Kontak</a>
                        </li>
                        
                        @guest
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                                </a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="btn-daftar-pill" href="{{ route('register') }}">
                                    Daftar <i class="bi bi-chevron-right small"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown ms-lg-2">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(Auth::user()->isAdmin())
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i> Dashboard Admin
                                        </a></li>
                                    @elseif(Auth::user()->isCustomer())
                                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i> Dashboard Saya
                                        </a></li>
                                    @elseif(Auth::user()->isDriver())
                                        <li><a class="dropdown-item" href="{{ route('driver.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i> Dashboard Driver
                                        </a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right"></i> Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container py-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container py-2">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="bi bi-car-front-fill text-warning"></i> Prasetya Rent Car</h5>
                    <p class="text-white-50">Layanan rental mobil terpercaya dengan armada lengkap dan harga terjangkau.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li><a href="{{ route('cars.index') }}" class="text-white-50 text-decoration-none">Katalog Mobil</a></li>
                        <li><a href="{{ route('about') }}" class="text-white-50 text-decoration-none">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="bi bi-telephone-fill"></i> +62 812-3456-789</li>
                        <li><i class="bi bi-envelope-fill"></i> info@prasetyarentcar.com</li>
                        <li><i class="bi bi-geo-alt-fill"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} Prasetya Rent Car. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    @yield('scripts')
</body>
</html>
