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
                    <option value="cod">COD</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Pemesanan</label>
                <input type="text" class="form-control bg-light" value="{{ now()->format('Y-m-d') }}" readonly>
            </div>

            @foreach($produk as $item)
                <input type="hidden" name="produk[]" value="{{ json_encode($item) }}">
            @endforeach

            <input type="hidden" name="total" value="{{ $total }}">

            <div class="text-center mt-4">
                <button type="submit" class="btn-checkout">
                    Pesan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
