@extends('layouts.pembeli')

@section('title', 'Detail Produk')

@section('content')
<style>
    .produk-container { padding: 30px 15px; }
    .produk-img-zoom {
        overflow: hidden; border-radius: 20px; border: 3px dashed #fff;
        box-shadow: 0 0 0 3px #ccc inset;
    }
    .produk-img-zoom img {
        width: 100%; object-fit: contain;
        transition: transform 0.5s ease; border-radius: 20px;
    }
    .produk-img-zoom:hover img { transform: scale(1.15); }

    .btn-khaki {
        background: #E5CBB7; color: #000; border: none;
        padding: 10px 20px; font-size: 1.1rem;
    }
    .btn-khaki:hover { background: #b39877; color: #000; }

    .wishlist-icon.active { color: #dc3545; stroke: none; }

    .produk-deskripsi {
        background: #fff; border-radius: 15px; padding: 30px;
        border: 1px solid rgba(0,0,0,0.1); margin-top: 40px;
    }
</style>

<div class="container-fluid produk-container">
    <div class="row g-4">
        {{-- Gambar Produk --}}
        <div class="col-md-6">
            <div class="produk-img-zoom">
                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}">
            </div>
        </div>

        {{-- Info Produk --}}
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">{{ $produk->nama }}</h2>
            <p class="text-muted">
                <strong>Merek:</strong> {{ $produk->merek ?? '-' }} |
                <strong>Kategori:</strong> {{ ucfirst($produk->kategori) }}
            </p>
            <p class="fs-4 text-primary fw-semibold">
                Rp <span id="harga-satuan">{{ number_format($produk->harga, 0, ',', '.') }}</span>
            </p>

            {{-- Input Jumlah --}}
            <label for="qty" class="form-label">Jumlah:</label>
            <input type="number" id="qty" class="form-control w-25 mb-3"
                value="1" min="1" max="{{ $produk->stok }}"
                {{ $produk->stok < 1 ? 'disabled' : '' }}>

            {{-- Total Harga --}}
            <label>Total Harga:</label>
            <p class="fs-5 text-success fw-bold">
                Rp <span id="total-harga">{{ number_format($produk->harga, 0, ',', '.') }}</span>
            </p>

            {{-- Notifikasi Stok Habis --}}
            @if ($produk->stok < 1)
                <div class="alert alert-danger">Maaf, stok habis.</div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="d-flex gap-3 flex-wrap mb-4">
                @if ($produk->stok > 0)
                    {{-- Tombol Beli --}}
                    <form action="{{ route('checkout.storeProduk') }}" method="POST">
                        @csrf
                        @foreach (['id','nama','harga','gambar'] as $field)
                            <input type="hidden" name="produk[{{ $produk->id }}][{{ $field }}]" value="{{ $produk->$field }}">
                        @endforeach
                        <input type="hidden" name="produk[{{ $produk->id }}][jumlah]" id="form-beli-jumlah" value="1">
                        <input type="hidden" name="produk[{{ $produk->id }}][total]" id="form-beli-total" value="{{ $produk->harga }}">
                        <input type="hidden" name="produk[{{ $produk->id }}][check]" value="1">
                        <input type="hidden" name="langsung_beli" value="true">
                        <button type="submit" class="btn btn-khaki rounded-pill d-flex align-items-center gap-2">
                            <img src="{{ asset('svg/tasbelanja.svg') }}" width="22"> Pesan Sekarang
                        </button>
                    </form>

                    {{-- Tombol Tambah ke Keranjang --}}
                    <form action="{{ route('keranjang.store') }}" method="POST">
                        @csrf
                        @foreach (['id','nama','harga','gambar'] as $field)
                            <input type="hidden" name="{{ $field }}" value="{{ $produk->$field }}">
                        @endforeach
                        <input type="hidden" name="jumlah" id="form-keranjang-jumlah" value="1">
                        <input type="hidden" name="total" id="form-keranjang-total" value="{{ $produk->harga }}">
                        <button type="submit" class="btn btn-khaki rounded-pill d-flex align-items-center gap-2">
                            <img src="{{ asset('svg/plus.svg') }}" width="22"> Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary rounded-pill" disabled>Stok Habis</button>
                @endif
            </div>

            {{-- Wishlist --}}
            <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                @auth
                    <form action="{{ route('wishlist.tambah') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                        <button type="submit" class="btn btn-sm text-dark d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                 width="20" height="20" class="{{ in_array($produk->id, $wishlist ?? []) ? 'wishlist-icon active' : 'wishlist-icon' }}">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 
                                      4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 
                                      4.5 0 0 1 0-6.364z" />
                            </svg>
                            Tambahkan ke Kesukaan
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" stroke="currentColor" fill="none"
                             viewBox="0 0 24 24">
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
    <div class="produk-deskripsi">
        <h3>Deskripsi Produk</h3>
        <p>{!! nl2br(e($produk->deskripsi ?? 'Tidak ada deskripsi tersedia.')) !!}</p>
    </div>
</div>

<script>
    const qtyInput = document.getElementById('qty');
    const totalHargaDisplay = document.getElementById('total-harga');
    const hargaSatuan = {{ $produk->harga }};
    const maxStok = {{ $produk->stok }};
    const formBeliJumlah = document.getElementById('form-beli-jumlah');
    const formBeliTotal = document.getElementById('form-beli-total');
    const formKeranjangJumlah = document.getElementById('form-keranjang-jumlah');
    const formKeranjangTotal = document.getElementById('form-keranjang-total');

    qtyInput?.addEventListener('input', function () {
        let qty = parseInt(this.value) || 1;
        qty = Math.max(1, Math.min(qty, maxStok));

        const total = qty * hargaSatuan;
        totalHargaDisplay.textContent = total.toLocaleString('id-ID');
        formBeliJumlah.value = qty;
        formBeliTotal.value = total;
        formKeranjangJumlah.value = qty;
        formKeranjangTotal.value = total;
    });
</script>
@endsection
