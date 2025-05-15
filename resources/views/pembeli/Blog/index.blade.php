@extends('layouts.pembeli')

@section('title', 'Blog')

@section('content')
<style>
    .blog-container {
        padding: 40px 20px;
    }

    .blog-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 30px;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .blog-card:hover {
        transform: translateY(-5px);
    }

    .blog-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .blog-content {
        padding: 20px;
        flex-grow: 1;
    }

    .blog-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #007bff;
        margin-bottom: 10px;
    }

    .blog-meta {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 10px;
    }

    .blog-desc {
        font-size: 0.95rem;
        color: #444;
        line-height: 1.6;
    }

    .read-more {
        margin-top: 15px;
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 8px 16px;
        font-weight: bold;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .read-more:hover {
        background-color: #0056b3;
    }

    .sidebar-title {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .search-box {
        position: relative;
        margin-bottom: 15px;
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
    }

    .text-empty {
        color: #666;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        margin: 30px 0;
    }

    .text-plain {
        color: #666;
        font-size: 0.95rem;
        text-align: center;
        margin: 10px 0;
    }
</style>

<div class="container blog-container">
    <div class="row">
        {{-- Kolom Artikel --}}
        <div class="col-lg-8">
            <h3 class="mb-4 fw-bold">Blog</h3>
            <div class="row">
                @if($artikels->isEmpty())
                    <p class="text-empty">No Posts Found</p>
                @else
                    @foreach($artikels as $artikel)
                        <div class="col-md-6 mb-4">
                            <div class="blog-card">
                                <img src="{{ asset('storage/' . $artikel->gambar) }}" alt="{{ $artikel->judul }}" class="blog-img">
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span>By Admin</span> • <span>{{ \Carbon\Carbon::parse($artikel->created_at)->format('d F Y') }}</span>
                                    </div>
                                    <h3 class="blog-title">{{ $artikel->judul }}</h3>
                                    <p class="blog-desc">{{ Str::limit(strip_tags($artikel->konten), 100) }}</p>
                                    <a href="{{ route('pembeli.blog.show', $artikel->id) }}" class="read-more">Read More</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
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

            <div class="artikel-wrapper mt-4">
                <h5 class="fw-bold mb-3 text-center">Artikel Terbaru</h5>
                <ul class="artikel-terbaru">
                    @if($latestArtikels->isEmpty())
                        <p class="text-plain w-100">Belum ada artikel terbaru.</p>
                    @else
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
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
