<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Khakiel Petshop')</title>

    {{-- Bootstrap & Font --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <link href="{{ asset('css/layoutcss/pembeli.css') }}" rel="stylesheet">

    {{-- AlpineJS --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .badge-circle {
            background-color: #0d6efd !important;
            color: white;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            padding: 0;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 font-main">

    {{-- Header --}}
    <header class="bg-krem py-3 px-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center w-100">
            {{-- Logo --}}
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/icon.png') }}" alt="Logo" style="width: 5.8rem; height: 5.8rem;">
                <div>
                    <h1 class="font-luckiest text-dark fs-5 mb-0">Khakiel Petshop</h1>
                    <small class="text-muted">Kebutuhan hewan kucing terlengkap</small>
                </div>
            </div>

            {{-- Pencarian --}}
            <form action="{{ route('pembeli.produk.index') }}" method="GET" class="form-pencarian">
                <div class="input-wrapper">
                    <input type="text" name="search" class="form-control custom-search" placeholder="Cari produk..." value="{{ request('search') }}">
                    <img src="{{ asset('svg/search.svg') }}" class="search-icon" alt="Search">
                </div>
            </form>

            {{-- Ikon --}}
            <div class="d-flex align-items-center gap-3">
                {{-- Person --}}
                @auth
                    <div x-data="{ open: false }" class="position-relative">
                        <button @click="open = !open" class="bg-transparent border-0 p-0">
                            <img src="{{ asset('svg/people.svg') }}" width="34" alt="User">
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="position-absolute end-0 mt-2 bg-white border rounded shadow-sm p-2"
                            style="min-width: 180px; z-index: 1000;">
                            <div class="px-2 py-1 text-muted small border-bottom">{{ Auth::user()->email }}</div>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-start w-100 px-2 py-1 text-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}">
                        <img src="{{ asset('svg/people.svg') }}" width="34" alt="Login">
                    </a>
                @endauth

                {{-- Wishlist --}}
                @php $wishlistCount = session('wishlist') ? count(session('wishlist')) : 0; @endphp
                <a href="{{ route('wishlist.index') }}" class="position-relative">
                    <img src="{{ asset('svg/heart.svg') }}" width="34" alt="Wishlist">
                    @if($wishlistCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge badge-circle">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                {{-- Keranjang --}}
                @php $jumlahKeranjang = session('keranjang') ? count(session('keranjang')) : 0; @endphp
                <a href="{{ route('keranjang.index') }}" class="position-relative">
                    <img src="{{ asset('svg/keranjang.svg') }}" width="34" alt="Keranjang">
                    @if($jumlahKeranjang > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge badge-circle">
                            {{ $jumlahKeranjang }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    {{-- Navbar --}}
    <nav class="bg-krem py-2">
        <div class="container d-flex justify-content-center align-items-center gap-4 flex-nowrap fw-semibold flex-md-row flex-column text-center">
            <a href="{{ route('pembeli.blog.index') }}" class="nav-link {{ request()->routeIs('pembeli.blog*') ? 'fw-semibold text-active' : 'text-dark' }}">Blog</a>
            <a href="{{ route('pembeli.informasipesanan.index') }}" class="nav-link {{ request()->is('informasi-pesanan*') ? 'fw-semibold text-active' : 'text-dark' }}">Status Pesanan</a>
            
            {{-- Dropdown Kategori --}}
            @php
                $kategori = match(request('kategori')) {
                    'makanan-kucing' => 'Makanan Kucing',
                    'aksesoris' => 'Aksesoris',
                    'perlengkapan' => 'Perlengkapan',
                    'obat-obatan' => 'Obat-obatan',
                    'vitamin-kucing' => 'Vitamin',
                    default => 'Kebutuhan Kucing',
                };
            @endphp
            <div class="dropdown">
                <button class="btn fw-semibold text-dark dropdown-toggle" type="button">
                    🐾 {{ $kategori }}
                </button>
                <ul class="dropdown-menu dropdown-menu-krem">
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index') }}">🐾 Semua Produk</a></li>
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index', ['kategori' => 'makanan-kucing']) }}">🐾 Makanan Kucing</a></li>
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index', ['kategori' => 'aksesoris']) }}">🐾 Aksesoris</a></li>
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index', ['kategori' => 'perlengkapan']) }}">🐾 Perlengkapan</a></li>
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index', ['kategori' => 'obat-obatan']) }}">🐾 Obat-obatan</a></li>
                    <li><a class="dropdown-item" href="{{ route('pembeli.produk.index', ['kategori' => 'vitamin-kucing']) }}">🐾 Vitamin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main --}}
    <main class="container py-5">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-krem py-4 mt-auto text-dark">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-4 px-2">
            <div class="footer-info text-start ps-2">
                <h5 class="font-luckiest mb-1">Khakiel Petshop</h5>
                <p>Kebutuhan hewan kucing terlengkap</p>
                <p>Jl. Pamayahan No.20, Kukusan</p>
                <p>Kecamatan Lohbener, Kab Indramayu, Jawa Barat 65252</p>
                <p>Pusat Kebutuhan Hewan Peliharaan Terlengkap, Terbesar, & Terpercaya No.1 di Indonesia</p>
            </div>
            <div class="mt-3 text-start ps-2">
                <a href="https://api.whatsapp.com/send?phone=6287717649173" target="_blank"
                class="text-decoration-none d-inline-flex align-items-center gap-2 fw-semibold text-dark">
                    <img src="{{ asset('svg/whatsapp.svg') }}" width="20" height="20" alt="WhatsApp">
                    <span>WhatsApp | 087-717-649-173</span>
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
