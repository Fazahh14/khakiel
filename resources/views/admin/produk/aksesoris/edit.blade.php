@extends('layouts.admin')

@section('title', 'Edit Produk Aksesoris')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/produk/aksesoris/edit.css') }}">
@endpush

@section('content')
@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<div class="form-wrapper">
    <h2>Edit Produk Aksesoris</h2>

    <form action="{{ route('admin.aksesoris.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nama Produk --}}
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Produk</label>
            <input type="text" name="nama" id="nama" class="form-control" required
                   value="{{ old('nama', $produk->nama) }}">
            @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <input type="text" name="kategori" id="kategori" class="form-control" required
                   value="{{ old('kategori', $produk->kategori) }}">
            @error('kategori') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Stok --}}
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" name="stok" id="stok" class="form-control" required
                   value="{{ old('stok', $produk->stok) }}">
            @error('stok') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Harga --}}
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" name="harga" id="harga" class="form-control" required
                   value="{{ old('harga', $produk->harga) }}">
            @error('harga') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            @error('deskripsi') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Gambar Produk --}}
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Produk</label>
            <input type="file" name="gambar" id="gambar" class="form-control">
            @error('gambar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

            <div class="d-flex justify-content-start mt-3">
                <div class="card-gambar">
                    @if($produk->gambar)
                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="Gambar Produk">
                    @else
                        <div class="card-body text-muted d-flex justify-content-center align-items-center" style="height: 100%;">
                            Belum ada gambar
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-simpan px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
