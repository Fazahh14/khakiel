@extends('layouts.auth')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mt-3">
        <div class="col-lg-8 rounded-4 shadow p-4 d-flex flex-column flex-md-row align-items-center justify-content-between"
            style="background-color: #E5CBB7;">

            {{-- Form Register --}}
            <div class="col-md-6 px-4">
                <h2 class="text-center fw-bold text-dark mb-4 mt-2">Daftar</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="name" class="form-label text-dark">Nama</label>
                        <input id="name" type="text" name="name" placeholder="Masukan Nama Anda"
                            value="{{ old('name') }}" required
                            class="form-control @error('name') is-invalid @enderror bg-white">

                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label text-dark">Email</label>
                        <input id="email" type="email" name="email" placeholder="Masukan Email Anda"
                            value="{{ old('email') }}" required
                            class="form-control @error('email') is-invalid @enderror bg-white">

                        @error('email')
                            @if(str_contains($message, 'unique'))
                                <div class="invalid-feedback d-block">Wah, emailnya udah dipakai orang lain nih 😅</div>
                            @elseif(str_contains($message, '@gdwgy.com'))
                                <div class="invalid-feedback d-block">Email harus menggunakan domain @gdwgy.com</div>
                            @else
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label text-dark">Password</label>
                        <input id="password" type="password" name="password" placeholder="Masukan Password Anda"
                            required
                            class="form-control @error('password') is-invalid @enderror bg-white">

                        @if($errors->has('password'))
                            @if(str_contains($errors->first('password'), 'confirmation'))
                                <div class="invalid-feedback d-block">Eits! Password dan ulangi password-nya nggak kembar nih 😬</div>
                            @else
                                <div class="invalid-feedback d-block">Password-nya kurang greget! Tambahin huruf BESAR, angka & simbol 💪</div>
                            @endif
                        @endif
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label text-dark">Ulangi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Masukan Kembali Password Anda" required
                            class="form-control bg-white">
                    </div>

                    {{-- Tombol Daftar --}}
                    <div class="text-center mb-3">
                        <button type="submit" class="btn bg-light text-dark px-5 border">
                            Daftar
                        </button>
                    </div>
                </form>

                <p class="text-center text-muted small mt-3">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-decoration-none text-primary">Masuk</a>
                </p>
            </div>

            {{-- Gambar --}}
            <div class="col-md-6 text-center mt-4 mt-md-0">
                <img src="{{ asset('images/kucing.png') }}" alt="Register Image" class="img-fluid" style="max-width: 250px;">
            </div>
        </div>
    </div>
</div>
@endsection
