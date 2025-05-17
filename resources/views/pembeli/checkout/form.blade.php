@extends('layouts.pembeli')

@section('title', 'Checkout')

@push('styles')
<style>
    body {
        background-color: #f9f9f9;
    }

    .checkout-wrapper {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        padding: 40px;
        max-width: 700px;
        margin: 0 auto;
    }

    .btn-checkout {
        background-color: #198754;
        color: white;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
    }

    .btn-checkout:hover {
        background-color: #146c43;
    }
</style>
@endpush

@section('content')
<div class="container py-5 px-3">
    <div class="checkout-wrapper">
        <h2 class="text-center mb-4">Formulir Pemesanan</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Pemesan</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Pemesan</label>
                <input type="text" name="alamat" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="telepon" class="form-label">No Telepon</label>
                <input type="text" name="telepon" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="metode" class="form-label">Metode Pembayaran</label>
                <select name="metode" class="form-select" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="midtrans">Midtrans (VA/Qris)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan</label>
                <input type="datetime-local" name="tanggal_pemesanan" class="form-control" 
                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>

            @foreach($produk as $index => $item)
                @php
                    $jumlah = $item['jumlah'] ?? 1;
                    $harga = $item['harga'];
                    $subtotal = $jumlah * $harga;
                @endphp
                <div class="mb-3 border rounded p-3 produk-item">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" value="{{ $item['nama'] }}" readonly>

                    <label class="form-label mt-2">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" 
                           value="Rp {{ number_format($harga, 0, ',', '.') }}" 
                           readonly data-harga="{{ $harga }}">

                    <label for="produk[{{ $index }}][jumlah]" class="form-label mt-2">Jumlah</label>
                    <input type="number" name="produk[{{ $index }}][jumlah]" class="form-control jumlah-input" 
                           value="{{ $jumlah }}" min="1" required>

                    <label class="form-label mt-2">Total Harga</label>
                    <input type="text" class="form-control total-harga" 
                           value="Rp {{ number_format($subtotal, 0, ',', '.') }}" readonly>

                    {{-- Hidden inputs --}}
                    <input type="hidden" name="produk[{{ $index }}][id]" value="{{ $item['id'] }}">
                    <input type="hidden" name="produk[{{ $index }}][nama]" value="{{ $item['nama'] }}">
                    <input type="hidden" name="produk[{{ $index }}][harga]" value="{{ $harga }}">
                    <input type="hidden" name="produk[{{ $index }}][gambar]" value="{{ $item['gambar'] }}">
                    <input type="hidden" name="produk[{{ $index }}][check]" value="{{ $item['check'] ?? 1 }}">
                </div>
            @endforeach

            <div class="mb-3 text-end">
                <h5>Total Keseluruhan: <span id="grand-total-text">Rp {{ number_format($total, 0, ',', '.') }}</span></h5>
                <input type="hidden" name="total" id="grand-total" value="{{ $total }}">
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn-checkout">Pesan Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const jumlahInputs = document.querySelectorAll(".jumlah-input");
        const grandTotalInput = document.getElementById("grand-total");
        const grandTotalText = document.getElementById("grand-total-text");

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function hitungTotal() {
            let total = 0;

            document.querySelectorAll('.produk-item').forEach(item => {
                const jumlah = parseInt(item.querySelector('.jumlah-input').value) || 0;
                const harga = parseInt(item.querySelector('.harga-satuan').dataset.harga) || 0;
                const subtotal = jumlah * harga;
                item.querySelector('.total-harga').value = formatRupiah(subtotal);
                total += subtotal;
            });

            grandTotalInput.value = total;
            grandTotalText.textContent = formatRupiah(total);
        }

        jumlahInputs.forEach(input => {
            input.addEventListener("input", hitungTotal);
        });

        hitungTotal(); // kalkulasi awal
    });
</script>
@endpush
