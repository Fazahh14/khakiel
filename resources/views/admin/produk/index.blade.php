@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')

<style>
    body {
        background-color: #f9f5eb;
    }

    .container-produk {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 85vh;
        margin-top: 20px;
    }

    .produk-row {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 40px;
    }

    .box {
        width: 220px;
        height: 200px;
        background-color: #E5CBB7;
        border-radius: 10px;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: bold;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-decoration: none;
        color: #000;
    }

    .box:hover {
        transform: scale(1.05);
        box-shadow: 4px 6px 12px rgba(0, 0, 0, 0.3);
        color: #000;
    }

    .box i {
        font-size: 30px;
        margin-bottom: 10px;
    }
</style>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>

<div class="container-produk">

    <!-- Baris atas -->
    <div class="produk-row">
        <a href="{{ route('admin.makanan-kucing.index') }}" class="box">
            <i class="fas fa-utensils"></i>
            Makanan Kucing
        </a>
        <a href="{{ route('admin.aksesoris.index') }}" class="box">
            <i class="fas fa-paw"></i>
            Aksesoris
        </a>
    </div>

    <!-- Baris bawah -->
    <div class="produk-row">
        <a href="{{ route('admin.obat-obatan.index') }}" class="box">
            <i class="fas fa-pills"></i>
            Obat-obatan
        </a>
        <a href="{{ route('admin.perlengkapan.index') }}" class="box">
            <i class="fas fa-tshirt"></i>
            Perlengkapan
        </a>
        <a href="{{ route('admin.vitamin-kucing.index') }}" class="box">
            <i class="fas fa-prescription-bottle-alt"></i>
            Vitamin Kucing
        </a>
    </div>

</div>
@endsection
