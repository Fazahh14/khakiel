@extends('layouts.pembeli')

@section('title', 'Produk Disukai')

@push('styles')
<style>
    .wishlist-row {
        background-color: #fdf1e8;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .wishlist-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #ccc;
    }

    .wishlist-info {
        flex: 1;
    }

    .wishlist-info h5 {
        font-size: 1rem;
        color: #00a;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .wishlist-info p {
        margin: 0;
        color: #333;
    }

    .wishlist-stok {
        min-width: 160px;
        text-align: center;
        font-weight: 600;
    }

    .wishlist-stok span {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .stok-terbatas {
        background-color: #fff3cd;
        color: #856404;
    }

    .stok-habis {
        border: 2px dashed #888;
        padding: 6px 12px;
        font-weight: 600;
        color: #444;
    }

    .stok-tersedia {
        color: #198754;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Produk yang Disukai</h2>

    @forelse ($wishlist as $item)
        <div class="wishlist-row">
            <img src="{{ asset('storage/' . $item['gambar']) }}" class="wishlist-img" alt="{{ $item['nama'] }}">

            <div class="wishlist-info">
                <h5>{{ $item['nama'] }}</h5>
                <p>Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
            </div>

            <div class="wishlist-stok">
                @if (!isset($item['stok']))
                    <span class="text-muted">Stok tidak diketahui</span>
                @elseif($item['stok'] == 0)
                    <span class="stok-habis">Stok habis</span>
                @elseif($item['stok'] == 1)
                    <span class="stok-terbatas">hanya 1 tersisa di stok</span>
                @else
                    <span class="stok-tersedia">tersedia</span>
                @endif
            </div>
        </div>
    @empty
        <div class="text-muted text-center">Belum ada produk disukai.</div>
    @endforelse
</div>
@endsection
