@extends('layouts.pembeli')

@section('title', 'Status Pesanan Saya')

@push('styles')
<style>
    /* Card styling */
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background-color: #fff;
    }

    /* Table full width with clear borders */
    table.table {
        width: 100%;
        border-collapse: collapse; /* untuk border nyambung */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header */
    thead tr {
        background-color: #0d6efd; /* Bootstrap blue */
        color: white;
    }

    thead th {
        padding: 12px 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
        vertical-align: middle;
        border: 1px solid #dee2e6; /* border antar kolom header */
    }

    /* Body rows */
    tbody tr {
        background-color: #f9f9f9;
        transition: background-color 0.3s ease;
    }
    tbody tr:hover {
        background-color: #e7f1ff;
    }

    tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        font-size: 0.95rem;
        border: 1px solid #dee2e6; /* border antar kolom dan baris isi */
    }

    /* Nama produk kiri rata */
    tbody td.text-start {
        text-align: left;
    }

    /* Badge status */
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 90px;
    }
    .badge-belum-diproses {
        background-color: #6c757d;
        color: white;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 90px;
    }
    .badge-sedang-diproses {
        background-color: #0dcaf0;
        color: #212529;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 90px;
    }
    .badge-selesai {
        background-color: #198754;
        color: white;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 90px;
    }

    /* Responsive table container */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@section('content')
<div class="container page-content">
    <div class="card p-4">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Status Pesanan Saya</h2>

        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="alert alert-success text-center" role="alert" id="flash-message">
                {{ session('success') }}
            </div>
        @endif

        {{-- Alert error --}}
        @if(session('error'))
            <div class="alert alert-danger text-center" role="alert" id="flash-message">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover text-center align-middle" id="statusTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Tanggal Pesanan</th>
                        <th class="text-start">Nama Produk</th>
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
                        <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y H:i') }}</td>
                        <td class="text-start">
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
                                $status = $transaksi->status;
                                $badgeClass = match($status) {
                                    'pending' => 'badge badge-pending',
                                    'belum diproses' => 'badge badge-belum-diproses',
                                    'sedang diproses' => 'badge badge-sedang-diproses',
                                    'selesai' => 'badge badge-selesai',
                                    default => 'badge bg-light text-dark',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-muted">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pesanans->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $pesanans->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
