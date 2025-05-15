@extends('layouts.pembeli')

@section('title', 'Detail Produk')

@section('content')

<style>
    .produk-container {
        padding: 30px 15px;
    }

    .produk-img-zoom {
        overflow: hidden;
        border-radius: 20px;
        position: relative;
        height: auto;
        border: 3px dashed #ffffff;
        box-shadow: 0 0 0 3px #ccc inset;
    }

    .produk-img-zoom img {
        width: 100%;
        height: auto;
        object-fit: contain;
        transition: transform 0.6s ease;
        transform-origin: center center;
        border-radius: 20px;
    }

    .produk-img-zoom:hover img {
        transform: scale(1.15);
    }

    .btn-khaki {
        background-color: #E5CBB7;
        color: #000;
        border: none;
        font-size: 1.1rem;
        padding: 10px 20px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-khaki:hover {
        background-color: #b39877 !important;
        color: #000 !important;
    }

    .btn-khaki img {
        width: 22px;
        height: 22px;
    }

    .wishlist-icon.active {
        fill: currentColor;
        stroke: none;
        color: #dc3545;
    }

    .produk-deskripsi {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 30px;
        margin-top: 40px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .produk-deskripsi h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .produk-deskripsi p {
        font-size: 0.95rem;
        color: #374151;
        line-height: 1.7;
        text-align: justify;
    }

    .btn-wrapper {
        margin-bottom: 40px;
    }
</style>

<div class="container-fluid produk-container">
    <div class="row g-4 align-items-start">

        {{-- Gambar --}}
        <div class="col-md-6">
            <div class="produk-img-zoom shadow-sm">
                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}">
            </div>
        </div>

        {{-- Info Produk --}}
        <div class="col-md-6">
            <h2 class="fs-2 fw-bold mb-3">{{ $produk->nama }}</h2>
            <p class="text-muted mb-1">
                <strong>Merek:</strong> <span class="text-primary">{{ $produk->merek ?? '-' }}</span> |
                <strong>Kategori:</strong> {{ ucfirst($produk->kategori) }}
            </p>
            <p class="fs-4 text-primary fw-semibold mb-4">
                Rp {{ number_format($produk->harga, 0, ',', '.') }}
            </p>

            {{-- Tombol --}}
            <div class="d-flex flex-wrap gap-4 btn-wrapper">

                {{-- Tombol Pesan Sekarang --}}
                <form action="{{ route('checkout.storeProduk') }}" method="POST">
                    @csrf
                    <input type="hidden" name="langsung_beli" value="true">
                    <input type="hidden" name="produk[{{ $produk->id }}][id]" value="{{ $produk->id }}">
                    <input type="hidden" name="produk[{{ $produk->id }}][nama]" value="{{ $produk->nama }}">
                    <input type="hidden" name="produk[{{ $produk->id }}][harga]" value="{{ $produk->harga }}">
                    <input type="hidden" name="produk[{{ $produk->id }}][jumlah]" value="1">
                    <input type="hidden" name="produk[{{ $produk->id }}][gambar]" value="{{ $produk->gambar ?? 'default.png' }}">
                    <input type="hidden" name="produk[{{ $produk->id }}][check]" value="1">

                    <button type="submit" class="btn btn-khaki rounded-pill d-flex align-items-center gap-2">
                        <img src="{{ asset('svg/tasbelanja.svg') }}"> <span>Pesan Sekarang</span>
                    </button>
                </form>

                {{-- Tombol Tambah ke Keranjang --}}
                <form action="{{ route('keranjang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $produk->id }}">
                    <input type="hidden" name="nama" value="{{ $produk->nama }}">
                    <input type="hidden" name="harga" value="{{ $produk->harga }}">
                    <input type="hidden" name="jumlah" value="1">
                    <input type="hidden" name="gambar" value="{{ $produk->gambar ?? 'default.png' }}">

                    <button type="submit" class="btn btn-khaki rounded-pill d-flex align-items-center gap-2">
                        <img src="{{ asset('svg/plus.svg') }}"> <span>Tambah ke Keranjang</span>
                    </button>
                </form>

            </div>

            {{-- Wishlist --}}
            <div class="d-flex justify-content-between align-items-center border rounded p-3">
                @auth
                    <form action="{{ route('wishlist.tambah') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                        <button type="submit" class="btn btn-sm text-dark d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" width="20" height="20"
                                 class="{{ in_array($produk->id, $wishlist ?? []) ? 'wishlist-icon active' : 'wishlist-icon' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 
                                      4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 
                                      4.5 0 0 1 0-6.364z" />
                            </svg>
                            Tambahkan ke Kesukaan
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 
                                  4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 
                                  4.5 0 0 1 0-6.364z" />
                        </svg>
                        Tambahkan ke Kesukaan
                    </a>
                @endauth

                <span class="text-muted small">Stok: {{ $produk->stok }}</span>
            </div>

        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="produk-deskripsi mt-5">
        <h3>Deskripsi Produk</h3>
        <p>{!! nl2br(e($produk->deskripsi ?? 'Tidak ada deskripsi tersedia.')) !!}</p>
    </div>
</div>

@endsection
