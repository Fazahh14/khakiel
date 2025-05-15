@extends('layouts.admin')

@section('title', 'Tambah Akun')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/akun/create.css') }}">
@endpush

@section('content')
<div class="container mt-5 animate-fade-slide d-flex justify-content-center">
    <div class="card custom-card shadow p-4 w-100" style="max-width: 900px;">
        <h2 class="text-center fw-bold mb-4">Tambah Akun</h2>

        <form action="{{ route('admin.akun.store') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="row mb-4 align-items-center">
                <label for="name" class="col-md-3 col-form-label text-end fw-semibold">Nama</label>
                <div class="col-md-7">
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="form-control input-field" placeholder="Nama Lengkap" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="row mb-4 align-items-center">
                <label for="email" class="col-md-3 col-form-label text-end fw-semibold">Email</label>
                <div class="col-md-7">
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="form-control input-field" placeholder="Email" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="row mb-4 align-items-center">
                <label for="password" class="col-md-3 col-form-label text-end fw-semibold">Password</label>
                <div class="col-md-7">
                    <input type="password" name="password" id="password"
                        class="form-control input-field" placeholder="Password" required>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Role --}}
            <div class="row mb-4 align-items-center">
                <label for="role" class="col-md-3 col-form-label text-end fw-semibold">Role</label>
                <div class="col-md-7">
                    <select name="role" id="role" class="form-select input-field" required>
                        <option disabled selected>Pilih Role</option>
                        <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Admin</option>
                        <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Pembeli</option>
                    </select>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tombol --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
