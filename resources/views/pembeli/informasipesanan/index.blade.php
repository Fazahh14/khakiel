@extends('layouts.pembeli')

@section('title', 'Status Pesanan Saya')

@push('styles')
<style>
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: #fff;
        padding: 1.5rem;
    }
    .table-responsive {
        overflow-x: auto;
    }
    table.table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 0.95rem;
        color: #333;
    }
    thead tr {
        background-color: #0d6efd;
        color: white;
        text-transform: uppercase;
        font-weight: 600;
    }
    thead th, tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        text-align: center;
        word-break: break-word;
    }
    tbody td.text-start {
        text-align: left;
    }
    tbody tr {
        background-color: #f9f9f9;
        transition: background-color 0.25s ease;
    }
    tbody tr:hover {
        background-color: #e7f1ff;
    }
    .produk-list div {
        margin-bottom: 6px;
    }
    .badge-status {
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 12px;
        display: inline-block;
        min-width: 90px;
        text-transform: capitalize;
        user-select: none;
    }
    .badge-sedang-diproses {
        background-color: #0dcaf0;
        color: #212529;
    }
    .badge-selesai {
        background-color: #198754;
        color: white;
    }
    .alert {
        font-weight: 600;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container page-content my-4">
    <div class="card">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Status Pesanan Saya</h2>

        @if(session('success'))
            <div class="alert alert-success text-center" id="flash-message">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger text-center" id="flash-message">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Tanggal Pesanan</th>
                        <th class="text-start">Produk</th>
                        <th>Qty</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $transaksi)
                        <tr>
                            <td>{{ ($pesanans->currentPage() - 1) * $pesanans->perPage() + $loop->iteration }}</td>
                            <td>{{ $transaksi->nama }}</td>
                            <td>{{ $transaksi->alamat }}</td>
                            <td>{{ $transaksi->telepon }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y') }}</td>
                            <td class="text-start produk-list">
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->produk?->nama ?? 'Produk tidak tersedia' }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->qty }}</div>
                                @endforeach
                            </td>
                            <td>{{ ucfirst($transaksi->metode) }}</td>
                            <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $status = strtolower($transaksi->status);
                                    $badgeClass = match($status) {
                                        'selesai' => 'badge-selesai',
                                        'sedang diproses', 'pending', 'belum diproses' => 'badge-sedang-diproses',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-muted text-center">Belum ada data pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pesanans->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $pesanans->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 4000);
        }
    });
</script>
@endpush
