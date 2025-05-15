<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Khakiel Petshop')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    {{-- Global Layout CSS --}}
    <link href="{{ asset('css/layoutcss/admin.css') }}" rel="stylesheet">

    {{-- Per View Custom Styles --}}
    @stack('styles')
</head>

<body class="font-main" style="opacity: 0;"> {{-- ✅ Tambahkan opacity: 0 untuk efek fade-in --}}

    {{-- Header --}}
    <header class="header-container d-flex justify-content-between align-items-center px-4 py-3" style="background-color: #E5CBB7;">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/icon.png') }}" alt="Logo" style="width: 4.5rem; height: 4.5rem;" class="me-3">
            <div>
                <h4 class="fw-bold mb-0" style="font-family: 'Luckiest Guy', cursive;">KHAKIEL PETSHOP</h4>
                <small class="text-secondary">Kebutuhan hewan kucing terlengkap</small>
            </div>
        </div>

        <div class="dropdown">
            <button class="btn border-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-fill" style="font-size: 2.7rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="dropdown-item" type="submit">Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    {{-- Navbar --}}
    <nav class="py-2" style="background-color: #E5CBB7;">
        <div class="container d-flex justify-content-center gap-3">
            <a href="{{ route('admin.akun.index') }}" class="nav-link {{ request()->is('akun*') ? 'text-primary fw-semibold' : 'text-dark' }}">Kelola Akun</a>
            <a href="{{ route('admin.produk.kelolaProduk') }}" class="nav-link {{ request()->is('admin/produk') ? 'text-primary fw-semibold' : 'text-dark' }}">Kelola Produk</a>
            <a href="{{ route('admin.artikel.index') }}" class="nav-link {{ request()->is('admin/artikel*') ? 'text-primary fw-semibold' : 'text-dark' }}">Kelola Artikel</a>
            <a href="{{ route('admin.kelolastatuspesanan.index') }}" class="nav-link {{ request()->is('admin/status-pesanan*') ? 'text-primary fw-semibold' : 'text-dark' }}">Kelola Status Pesanan</a>
            <a href="{{ route('admin.laporan.penjualan') }}" class="nav-link {{ request()->is('laporan/penjualan*') ? 'text-primary fw-semibold' : 'text-dark' }}">Laporan Penjualan</a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="px-4 py-5">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="px-4 py-4 text-muted" style="background-color: #E5CBB7;">
        <div class="footer-container d-flex flex-column flex-md-row justify-content-between gap-3">
            <div class="text-start">
                <h5 class="font-luckiest">Khakiel Petshop</h5>
                <p>Kebutuhan hewan kucing terlengkap</p>
                <p>Jl. Pamayahan No.20, Kukusan</p>
                <p>Kecamatan Lohbener, Kab Indramayu, Jawa Barat 65252</p>
                <p>Pusat Kebutuhan Hewan Peliharaan Terlengkap, Terbesar, & Terpercaya No.1 di Indonesia</p>
            </div>
            <div class="footer-contact text-end">
                <a href="https://api.whatsapp.com/send?phone=6287717649173" target="_blank" class="text-decoration-none d-inline-flex align-items-center gap-2 fw-semibold text-dark">
                    <i class="bi bi-whatsapp" style="font-size: 1.3rem;"></i>
                    <span>WhatsApp | 087-717-649-173</span>
                </a>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Fade in effect --}}
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            document.body.style.opacity = 1;
        });
        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        };
    </script>

    {{-- Tambahan script dari halaman --}}
    @stack('scripts')
</body>
</html>
