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
            <a href="{{ route('admin.akun.index') }}" class="nav-link {{ request()->is('admin/akun*') ? 'fw-semibold text-active' : 'text-dark' }}">Kelola Akun</a>
            <a href="{{ route('admin.produk.kelolaProduk') }}" class="nav-link {{ request()->is('admin/produk*') ? 'fw-semibold text-active' : 'text-dark' }}">Kelola Produk</a>
            <a href="{{ route('admin.artikel.index') }}" class="nav-link {{ request()->is('admin/artikel*') ? 'fw-semibold text-active' : 'text-dark' }}">Kelola Artikel</a>
            <a href="{{ route('admin.kelolastatuspesanan.index') }}" class="nav-link {{ request()->is('admin/status-pesanan*') ? 'fw-semibold text-active' : 'text-dark' }}">Kelola Status Pesanan</a>
            <a href="{{ route('admin.laporan.penjualan') }}" class="nav-link {{ request()->is('admin/laporan*') ? 'fw-semibold text-active' : 'text-dark' }}">Laporan Penjualan</a>             
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
  <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
</svg>
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
