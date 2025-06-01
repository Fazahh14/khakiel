@extends('layouts.pembeli')

@section('title', 'Produk Disukai')

@push('styles')
<style>
    .container {
        max-width: 900px;
        margin: auto;
        padding: 20px;
        font-family: 'Poppins', sans-serif;
        color: #333;
    }

    h2 {
        margin-bottom: 30px;
        font-weight: 700;
        font-size: 2rem;
        text-align: center;
        color: #222;
    }

    a.wishlist-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .wishlist-row {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        background-color: #fff;
        transition: box-shadow 0.3s ease;
        cursor: pointer;
    }

    .wishlist-row:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .wishlist-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 25px;
        flex-shrink: 0;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
    }

    .wishlist-info {
        flex: 1;
    }

    .wishlist-info h5 {
        margin: 0 0 8px 0;
        font-weight: 600;
        font-size: 1.25rem;
        color: #111;
    }

    .wishlist-info p {
        margin: 0;
        font-size: 1.1rem;
        color: #555;
        font-weight: 500;
    }

    .wishlist-stok {
        width: 140px;
        text-align: center;
        font-weight: 600;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .stok-tersedia {
        color: #2e7d32; /* hijau */
    }

    .stok-habis {
        color: #d32f2f; /* merah */
    }

    .stok-terbatas {
        color: #fbc02d; /* kuning */
    }

    .text-muted {
        color: #999 !important;
    }

    form button {
        background-color: #d32f2f;
        border: none;
        padding: 8px 14px;
        color: #fff;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.25s ease;
        flex-shrink: 0;
        margin-left: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }

    form button:hover {
        background-color: #b71c1c;
    }

    form button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(211,47,47,0.5);
    }

    /* Responsive */
    @media (max-width: 600px) {
        .wishlist-row {
            flex-direction: column;
            align-items: flex-start;
        }
        .wishlist-img {
            margin-bottom: 12px;
        }
        .wishlist-stok {
            width: 100%;
            margin-top: 8px;
            text-align: left;
        }
        form button {
            margin-left: 0;
            margin-top: 12px;
            width: 100%;
            justify-content: center;
        }
    }

    .empty-msg {
        text-align: center;
        color: #777;
        font-size: 1.1rem;
        margin-top: 50px;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h2>Produk yang Disukai</h2>

    @forelse ($wishlist as $item)
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <a href="{{ route('pembeli.produk.show', $item['id']) }}" class="wishlist-link" style="flex:1;">
                <div class="wishlist-row" data-id="{{ $item['id'] }}">
                    <img src="{{ asset('storage/' . $item['gambar']) }}" class="wishlist-img" alt="{{ $item['nama'] }}">

                    <div class="wishlist-info">
                        <h5>{{ $item['nama'] }}</h5>
                        <p>Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                    </div>

                    <div class="wishlist-stok">
                        @if (!isset($item['stok']))
                            <span class="text-muted stok-text">Stok tidak diketahui</span>
                        @elseif($item['stok'] == 0)
                            <span class="stok-habis stok-text">Stok habis</span>
                        @elseif($item['stok'] == 1)
                            <span class="stok-terbatas stok-text">Hanya 1 tersisa</span>
                        @else
                            <span class="stok-tersedia stok-text">Tersedia</span>
                        @endif
                    </div>
                </div>
            </a>

            <form action="{{ route('wishlist.hapus', $item['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini dari wishlist?')" style="margin-left: 15px;">
                @csrf
                @method('DELETE')
                <button type="submit" title="Hapus dari Wishlist">🗑️ Hapus</button>
            </form>
        </div>
    @empty
        <div class="empty-msg">Belum ada produk disukai.</div>
    @endforelse
</div>

<script>
    // Fungsi polling cek stok terbaru setiap 30 detik
    function fetchLatestStock() {
        const rows = document.querySelectorAll('.wishlist-row');

        rows.forEach(row => {
            const productId = row.getAttribute('data-id');

            fetch(`/api/product-stock/${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (data && typeof data.stok !== 'undefined') {
                        const stokSpan = row.querySelector('.stok-text');
                        // Update teks dan kelas warna stok
                        stokSpan.textContent = getStockText(data.stok);
                        stokSpan.className = 'stok-text ' + getStockClass(data.stok);
                    }
                })
                .catch(err => {
                    console.error('Error fetching stock:', err);
                });
        });
    }

    function getStockText(stok) {
        if (stok === null || typeof stok === 'undefined') {
            return 'Stok tidak diketahui';
        }
        if (stok == 0) return 'Stok habis';
        if (stok == 1) return 'Hanya 1 tersisa';
        if (stok > 1) return 'Tersedia';
        return 'Stok tidak diketahui';
    }

    function getStockClass(stok) {
        if (stok === null || typeof stok === 'undefined') {
            return 'text-muted';
        }
        if (stok == 0) return 'stok-habis';
        if (stok == 1) return 'stok-terbatas';
        if (stok > 1) return 'stok-tersedia';
        return 'text-muted';
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchLatestStock();
        setInterval(fetchLatestStock, 30000); // tiap 30 detik update stok
    });
</script>
@endsection
