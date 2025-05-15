@extends('layouts.admin')

@section('title', 'Edit Akun')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/akun/edit.css') }}">
@endpush

@section('content')
@if(session('success'))
<div class="container mt-4">
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
</div>
@endif

<div class="container mt-5 animate-fade-slide">
    <div class="form-section mx-auto">
        <h2 class="text-center fw-bold mb-4">Edit Akun</h2>

        <form action="{{ route('admin.akun.update', $akun->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="row mb-4 align-items-center">
                <label for="name" class="col-sm-3 col-form-label form-label">Nama</label>
                <div class="col-sm-9">
                    <input type="text" name="name" id="name" value="{{ $akun->name }}" required
                        class="form-control bg-light rounded-3">
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="row mb-4 align-items-center">
                <label for="email" class="col-sm-3 col-form-label form-label">Email</label>
                <div class="col-sm-9">
                    <input type="email" name="email" id="email" value="{{ $akun->email }}" required
                        class="form-control bg-light rounded-3">
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="row mb-4 align-items-center">
                <label for="password" class="col-sm-3 col-form-label form-label">Password</label>
                <div class="col-sm-9">
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak diubah"
                        class="form-control bg-light rounded-3">
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Role --}}
            <div class="row mb-4 align-items-center">
                <label for="role" class="col-sm-3 col-form-label form-label">Role</label>
                <div class="col-sm-9">
                    <select name="role" id="role" required class="form-select bg-light rounded-3">
                        <option disabled>Pilih Role</option>
                        <option value="seller" {{ $akun->role == 'seller' ? 'selected' : '' }}>Admin</option>
                        <option value="buyer" {{ $akun->role == 'buyer' ? 'selected' : '' }}>Pembeli</option>
                    </select>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
