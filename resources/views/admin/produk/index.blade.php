@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
<style>
    .svg-icon svg {
        width: 48px;
        height: 48px;
        fill: currentColor;
        transition: transform 0.3s ease;
    }

    .produk-card {
        background-color: #F3E5D5 !important;
        border-radius: 20px;
        opacity: 0;
        transform: scale(0.95) translateY(30px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.6s ease;
        color: #212529;
    }

    .produk-card:hover {
        transform: scale(1.05) translateY(-10px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.25);
    }

    .produk-card.fade-in {
        opacity: 1 !important;
        transform: scale(1) translateY(0) !important;
    }

    .produk-card.active {
        background-color: #0d6efd !important;
        color: white !important;
    }

    .produk-card.active h5 {
        color: white !important;
    }

    /* SVG tetap default */
    .svg-icon {
        color: #6c757d; /* text-secondary */
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center mb-4">
        {{-- Makanan Kucing --}}
        <div class="col-md-3 col-sm-6 mb-4 produk-wrapper">
            <a href="{{ route('admin.makanan-kucing.index') }}" class="text-decoration-none">
                <div class="card produk-card text-center shadow-sm h-100 
                    {{ request()->routeIs('makanan-kucing.index') ? 'active' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 svg-icon">
                            {!! file_get_contents(public_path('svg/makanan.svg')) !!}
                        </div>
                        <h5 class="fw-bold">Makanan Kucing</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Aksesoris --}}
        <div class="col-md-3 col-sm-6 mb-4 produk-wrapper">
            <a href="{{ route('admin.aksesoris.index') }}" class="text-decoration-none">
                <div class="card produk-card text-center shadow-sm h-100 
                    {{ request()->routeIs('aksesoris.index') ? 'active' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 svg-icon">
                            {!! file_get_contents(public_path('svg/akesoris.svg')) !!}
                        </div>
                        <h5 class="fw-bold">Aksesoris</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        {{-- Obat-obatan --}}
        <div class="col-md-3 col-sm-6 mb-4 produk-wrapper">
            <a href="{{ route('admin.obat-obatan.index') }}" class="text-decoration-none">
                <div class="card produk-card text-center shadow-sm h-100 
                    {{ request()->routeIs('obat-obatan.index') ? 'active' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 svg-icon">
                            {!! file_get_contents(public_path('svg/obat-obatan.svg')) !!}
                        </div>
                        <h5 class="fw-bold">Obat-obatan</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Perlengkapan --}}
        <div class="col-md-3 col-sm-6 mb-4 produk-wrapper">
            <a href="{{ route('admin.perlengkapan.index') }}" class="text-decoration-none">
                <div class="card produk-card text-center shadow-sm h-100 
                    {{ request()->routeIs('perlengkapan.index') ? 'active' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 svg-icon">
                            {!! file_get_contents(public_path('svg/perlengkapan.svg')) !!}
                        </div>
                        <h5 class="fw-bold">Perlengkapan</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Vitamin Kucing --}}
        <div class="col-md-3 col-sm-6 mb-4 produk-wrapper">
            <a href="{{ route('admin.vitamin-kucing.index') }}" class="text-decoration-none">
                <div class="card produk-card text-center shadow-sm h-100 
                    {{ request()->routeIs('vitamin-kucing.index') ? 'active' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <div class="mb-3 svg-icon">
                            {!! file_get_contents(public_path('svg/vitamin.svg')) !!}
                        </div>
                        <h5 class="fw-bold">Vitamin Kucing</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cards = document.querySelectorAll('.produk-card');
        const observer = new IntersectionObserver(entries => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('fade-in');
                    }, index * 100); // delay tiap card 100ms
                }
            });
        }, {
            threshold: 0.2
        });

        cards.forEach(card => observer.observe(card));
    });
</script>
@endsection
