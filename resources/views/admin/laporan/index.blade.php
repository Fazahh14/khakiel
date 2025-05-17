@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Rekap Harian Transaksi</h2>

    {{-- Form Cari Tanggal --}}
    <form method="GET" action="#" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="tanggal" class="form-label">Pilih Tanggal</label>
            <input type="date" name="tanggal" id="tanggal"
                   class="form-control" value="{{ request('tanggal') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>

    {{-- Tabel Rekap Harian --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Tanggal Pesanan</th>
                    <th scope="col">Nama Produk</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($rekap) && isset($rekap['items']) && count($rekap['items']) > 0)
                    @foreach($rekap['items'] as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y H:i') }}</td>
                            <td>{{ $item['nama_produk'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @elseif(request()->filled('tanggal'))
                    <tr>
                        <td colspan="4" class="text-center">
                            Tidak ada rekap harian pada tanggal
                            {{ \Carbon\Carbon::parse(request('tanggal'))->format('d-m-Y') }}.
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" class="text-center">Silakan pilih tanggal terlebih dahulu.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Total Pendapatan Harian --}}
    @if(isset($rekap) && isset($rekap['items']) && count($rekap['items']) > 0)
        <div class="mt-3">
            <h5>Jumlah Pendapatan Harian: <strong>Rp {{ number_format($rekap['total_harga'], 0, ',', '.') }}</strong></h5>
        </div>
    @endif
</div>
@endsection
