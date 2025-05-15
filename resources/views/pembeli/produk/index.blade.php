@extends('layouts.pembeli')

@section('title', 'Produk')

@section('content')
<style>
    .produk-card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        min-height: 280px; /* dikurangi agar tinggi seimbang */
        width: 100%;
        padding-left: 5px;
        padding-right: 5px;
    }

    .produk-card-hover:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .produk-img {
        height: 180px; /* dikurangi dari 220px */
        object-fit: cover;
        border-radius: 0.5rem;
        width: 100%;
        margin-bottom: 10px;
    }

    /* Custom col-xl-1/5 (20%) untuk 5 card dalam 1 baris */
    @media (min-width: 1200px) {
        .custom-xl-1-5 {
            width: 20%;
            flex: 0 0 20%;
        }
    }

    .btn-kecil {
        font-size: 0.7rem;
        padding: 3px 8px;
    }

    .card-title {
        font-size: 0.85rem;
    }

    .text-primary {
        font-size: 0.8rem;
    }
</style>

<div class="container py-5">
    <div class="row gx-4 gy-4">
        @foreach($produk as $item)
            <div class="col-6 col-md-4 col-lg-3 custom-xl-1-5">
                <div class="card produk-card-hover h-100 shadow-sm border-0 position-relative">
                    <a href="{{ route('pembeli.produk.show', $item->id) }}" class="stretched-link"></a>

                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="produk-img card-img-top">

                    <div class="card-body p-2">
                        <p class="text-primary fw-bold mb-1">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </p>

                        <h5 class="card-title fw-semibold mb-2 line-clamp-2">{{ $item->nama }}</h5>

                        <a href="{{ route('pembeli.produk.show', $item->id) }}" class="btn btn-primary btn-kecil rounded-pill">
                            Lihat selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
