@extends('layouts.admin')

@section('title', 'Tambah Produk Obat-obatan')

@section('content')
@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<style>
    .form-wrapper {
        background-color: #E5CBB7;
        border-radius: 20px;
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="form-wrapper">
    <h2 class="text-center mb-4 fw-bold">Tambah Produk</h2>

    <form action="{{ route('admin.obat-obatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nama Produk --}}
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Produk</label>
            <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                   class="form-control bg-light">
            @error('nama') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Stok --}}
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" name="stok" id="stok" value="{{ old('stok') }}" required
                   class="form-control bg-light">
            @error('stok') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Harga --}}
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" name="harga" id="harga" value="{{ old('harga') }}" required
                   class="form-control bg-light">
            @error('harga') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control bg-light">{{ old('deskripsi') }}</textarea>
            @error('deskripsi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Gambar Produk --}}
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Produk</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            @error('gambar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Tombol --}}
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-secondary px-4 fw-semibold">Simpan</button>
        </div>
    </form>
</div>
@endsection
