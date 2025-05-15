@extends('layouts.pembeli')

@section('title', $artikel->judul)

@section('content')
<style>
    .detail-container {
        padding: 40px 20px;
    }

    .artikel-header {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 15px;
        color: #222;
    }

    .artikel-meta {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 20px;
    }

    .artikel-img {
        width: 100%;
        max-height: 450px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    .artikel-konten {
        font-size: 1rem;
        color: #333;
        line-height: 1.8;
        text-align: justify;
    }

    .sidebar-title {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .search-box {
        position: relative;
        margin-bottom: 25px; /* Diperbesar jarak bawahnya */
    }

    .search-input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .search-box img {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        opacity: 0.6;
    }

    .artikel-terbaru {
        list-style: none;
        padding-left: 0;
    }

    .artikel-terbaru li {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
    }

    .artikel-terbaru img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .artikel-terbaru .info {
        font-size: 0.85rem;
        color: #333;
    }

    .artikel-terbaru .info small {
        display: block;
        color: #888;
        font-size: 0.75rem;
    }

    .artikel-wrapper {
        border: 2px dashed #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-top: 30px !important; /* Diperbesar jarak atasnya */
    }

    /* Tambahkan jarak horizontal antar kolom di layar besar */
    @media (min-width: 992px) {
        .col-lg-8 {
            padding-right: 85px;
        }

        .col-lg-4 {
            padding-left: 85px;
        }
    }
</style>

<div class="container detail-container">
    <div class="row">
        {{-- Kolom Artikel --}}
        <div class="col-lg-8">
            <h1 class="artikel-header">{{ $artikel->judul }}</h1>
            <div class="artikel-meta">
                By Admin • {{ \Carbon\Carbon::parse($artikel->created_at)->format('d F Y') }}
            </div>

            <img src="{{ asset('storage/' . $artikel->gambar) }}" alt="{{ $artikel->judul }}" class="artikel-img">

            <div class="artikel-konten">
                {!! $artikel->konten !!}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="sidebar-title">Cari</div>
            <div class="search-box">
                <form action="{{ route('pembeli.blog.index') }}" method="GET">
                    <input type="text" name="search" class="search-input" placeholder="Cari artikel..." value="{{ request('search') }}">
                    <img src="{{ asset('svg/search.svg') }}" alt="search icon">
                </form>
            </div>

            <div class="artikel-wrapper">
                <h5 class="fw-bold mb-3 text-center">Artikel Terbaru</h5>
                <ul class="artikel-terbaru">
                    @foreach($latestArtikels as $latest)
                        <li>
                            <img src="{{ asset('storage/' . $latest->gambar) }}" alt="{{ $latest->judul }}">
                            <div class="info">
                                <small>{{ \Carbon\Carbon::parse($latest->created_at)->format('d F Y') }}</small>
                                <a href="{{ route('pembeli.blog.show', $latest->id) }}" class="d-block text-decoration-none text-dark">
                                    {{ Str::limit($latest->judul, 40) }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
