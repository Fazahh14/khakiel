{{-- resources/views/pembeli/keranjangBelanja/index.blade.php --}}
@extends('layouts.pembeli')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    body { background-color: #f9f9f9; }
    .card-custom {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 0 10px rgba(0,0,0,0.04);
    }
    .produk-card img {
        object-fit: cover;
        width: 100px;
        height: 100px;
    }
    .produk-card { flex-wrap: wrap; }
    .input-pill-group {
        display: flex;
        align-items: center;
        border-radius: 50px;
        overflow: hidden;
        border: 1px solid #ced4da;
        background-color: #fff;
    }
    .input-pill-group .btn,
    .input-pill-group .jumlah {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    .jumlah {
        min-width: 40px;
        text-align: center;
        padding: 6px 10px;
        font-weight: 500;
    }
    .hapus-btn {
        background: none;
        border: none;
        padding: 0;
        margin-left: 5px;
        cursor: pointer;
    }
    .hapus-btn img {
        width: 18px;
        height: 18px;
    }
    @media (max-width: 576px) {
        .produk-card img { width: 80px; height: 80px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-5 px-3 px-md-5">
    <h1 class="h4 fw-bold mb-4">Keranjang Belanja</h1>

    @if (session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    @if (count($keranjang) > 0)
    <form action="{{ route('checkout.storeProduk') }}" method="POST">
        @csrf

        <div class="card card-custom mb-4">
            <div class="card-body d-flex align-items-center">
                <input type="checkbox" id="pilih-semua" class="form-check-input me-3">
                <label for="pilih-semua" class="form-check-label fw-semibold">
                    Pilih Semua (<span id="jumlah-dipilih">0</span> produk)
                </label>
            </div>
        </div>

        <div id="keranjang-list">
            @foreach ($keranjang as $id => $item)
            <div class="card card-custom mb-4" data-id="{{ $id }}">
                <div class="card-body d-flex produk-card align-items-center gap-3">
                    <input type="checkbox" name="produk[{{ $id }}][check]" value="1" class="form-check-input item-checkbox">

                    <div>
                        <img src="{{ !empty($item['gambar']) ? asset('storage/' . $item['gambar']) : asset('storage/default.png') }}" alt="{{ $item['nama'] }}" class="rounded">
                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-bold text-end">
                            Rp {{ number_format($item['harga'], 0, ',', '.') }}
                        </div>
                        <h5 class="fw-bold mb-2">{{ $item['nama'] }}</h5>

                        <div class="d-flex align-items-center gap-2">
                            <div class="input-pill-group">
                                <button class="btn btn-sm px-3" type="button" onclick="ubahJumlah('{{ $id }}', 'kurang')">−</button>
                                <span class="jumlah" id="jumlah-{{ $id }}">{{ $item['jumlah'] }}</span>
                                <button class="btn btn-sm px-3" type="button" onclick="ubahJumlah('{{ $id }}', 'tambah')">+</button>
                            </div>

                            <form id="hapus-form-{{ $id }}" action="{{ route('keranjang.hapus', $id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" class="hapus-btn" title="Hapus produk"
                                onclick="if (confirm('Hapus produk ini dari keranjang?')) document.getElementById('hapus-form-{{ $id }}').submit();">
                                <img src="{{ asset('svg/tempatsampah.svg') }}" alt="Hapus">
                            </button>
                        </div>

                        <div class="mt-2 text-muted small">
                            Total: Rp <span class="subtotal">{{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}</span>
                        </div>

                        {{-- Hidden input untuk kirim ke server hanya saat dicentang --}}
                        <input type="hidden" name="produk[{{ $id }}][id]" value="{{ $id }}" class="input-produk" disabled>
                        <input type="hidden" name="produk[{{ $id }}][nama]" value="{{ $item['nama'] }}" class="input-produk" disabled>
                        <input type="hidden" name="produk[{{ $id }}][harga]" value="{{ $item['harga'] }}" class="input-produk" disabled>
                        <input type="hidden" name="produk[{{ $id }}][jumlah]" value="{{ $item['jumlah'] }}" class="input-jumlah input-produk" disabled>
                        <input type="hidden" name="produk[{{ $id }}][gambar]" value="{{ $item['gambar'] }}" class="input-produk" disabled>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card card-custom">
            <div class="card-body">
                <div class="h5 mb-3 fw-semibold">
                    Total Semua: Rp <span id="total-harga">0</span>
                </div>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill" id="btn-checkout" disabled>
                    Checkout Sekarang
                </button>
            </div>
        </div>
    </form>
    @else
        <div class="alert alert-info text-center">Keranjang belanja kamu kosong.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function ubahJumlah(id, aksi) {
        const card = document.querySelector(`[data-id="${id}"]`);
        const jumlahSpan = card.querySelector('.jumlah');
        const jumlahInput = card.querySelector('.input-jumlah');
        let jumlah = parseInt(jumlahSpan.innerText);

        if (aksi === 'tambah') jumlah++;
        else if (aksi === 'kurang' && jumlah > 1) jumlah--;

        jumlahSpan.innerText = jumlah;
        jumlahInput.value = jumlah;

        const harga = parseInt(card.querySelector('.fw-bold.text-end').innerText.replace(/[Rp. ]/g, '').replace(/\./g, ''));
        const subtotalElem = card.querySelector('.subtotal');
        subtotalElem.innerText = (harga * jumlah).toLocaleString('id-ID');

        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('#keranjang-list > div').forEach(card => {
            if (card.querySelector('.item-checkbox').checked) {
                const harga = parseInt(card.querySelector('.fw-bold.text-end').innerText.replace(/[Rp. ]/g, '').replace(/\./g, ''));
                const jumlah = parseInt(card.querySelector('.jumlah').innerText);
                total += harga * jumlah;
            }
        });
        document.getElementById('total-harga').innerText = total.toLocaleString('id-ID');
    }

    function updateFormProdukTerpilih() {
        document.querySelectorAll('#keranjang-list > div').forEach(card => {
            const checked = card.querySelector('.item-checkbox').checked;
            card.querySelectorAll('.input-produk').forEach(input => {
                input.disabled = !checked;
            });
        });
    }

    function updateJumlahDipilih() {
        const selected = document.querySelectorAll('.item-checkbox:checked').length;
        document.getElementById('jumlah-dipilih').innerText = selected;
    }

    function toggleCheckoutButton() {
        const selected = document.querySelectorAll('.item-checkbox:checked').length;
        document.getElementById('btn-checkout').disabled = selected === 0;
    }

    document.getElementById('pilih-semua').addEventListener('change', function () {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
        updateJumlahDipilih();
        updateTotal();
        toggleCheckoutButton();
        updateFormProdukTerpilih();
    });

    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            updateJumlahDipilih();
            updateTotal();
            toggleCheckoutButton();
            updateFormProdukTerpilih();
        });
    });

    // Inisialisasi awal
    updateJumlahDipilih();
    updateTotal();
    toggleCheckoutButton();
    updateFormProdukTerpilih();
});
</script>
@endpush
