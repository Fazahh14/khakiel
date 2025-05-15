@extends('layouts.admin')

@section('title', 'Edit Produk Perlengkapan')

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
        max-width: 800px;
        margin: 0 auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .form-wrapper h2 {
        font-weight: bold;
        margin-bottom: 30px;
        text-align: center;
    }

    .btn-simpan {
        background-color: #d9d9d9;
        color: #000;
        font-weight: 600;
    }

    .btn-simpan:hover {
        background-color: #c6c6c6;
    }

    .card-gambar {
        width: 150px;
        height: 150px;
        overflow: hidden;
        border-radius: 10px;
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-gambar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .card-gambar .card-body {
        padding: 10px;
        text-align: center;
    }
</style>

<div class="form-wrapper">
    <h2>Edit Produk</h2>

    <form action="{{ route('admin.perlengkapan.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nama Produk --}}
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Produk</label>
            <input type="text" name="nama" id="nama" class="form-control" required
                   value="{{ old('nama', $produk->nama) }}">
            @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
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
