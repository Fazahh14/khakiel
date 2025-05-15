@extends('layouts.pembeli')

@section('title', 'Status Pesanan')

@push('styles')
<style>
    .card-box {
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        padding: 30px;
    }

    .table thead th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: 600;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.7em;
    }

    .pagination {
        margin-top: 20px;
        justify-content: end;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4">Status Pesanan</h2>

    <div class="card-box">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanans as $i => $transaksi)
                    <tr>
                        <td>{{ $i + $pesanans->firstItem() }}</td>
                        <td>
                            @php
                                $produkList = json_decode($transaksi->produk_json, true);
                            @endphp

                            @if ($produkList && is_array($produkList))
                                <ul class="list-unstyled mb-0 text-start">
                                    @foreach ($produkList as $item)
                                        <li>- {{ $item['nama'] ?? '-' }} (x{{ $item['jumlah'] ?? 1 }})</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $transaksi->tanggal_pesanan }}</td>
                        <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        <td>
                            @if ($transaksi->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif ($transaksi->status == 'belum diproses')
                                <span class="badge bg-secondary">Belum Diproses</span>
                            @elseif ($transaksi->status == 'sedang diproses')
                                <span class="badge bg-info text-dark">Sedang Diproses</span>
                            @elseif ($transaksi->status == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-light text-dark">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-muted">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pesanans->links() }}
    </div>
</div>
@endsection
