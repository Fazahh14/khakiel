@extends('layouts.admin')

@section('title', 'Kelola Status Pesanan')

@push('styles')
<style>
    .btn-simpan {
        background-color: #198754;
        color: white;
    }
    .btn-simpan:hover {
        background-color: #157347;
    }
    .btn-hapus {
        background-color: #dc3545;
        color: white;
    }
    .btn-hapus:hover {
        background-color: #bb2d3b;
    }
    .table-custom th {
        background-color: #f0f0f0;
        font-weight: 600;
    }
    .table-custom td, .table-custom th {
        vertical-align: middle;
        padding: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container page-content">
    <div class="card shadow rounded-4 p-4">
        <h2 class="text-center fw-bold text-uppercase mb-4 text-dark">Kelola Status Pesanan</h2>

        @if(session('success'))
            <div class="alert alert-success text-center" id="flash-message">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-center" id="flash-message">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-custom align-middle text-center rounded-4 overflow-hidden">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $transaksi)
                        <tr>
                            <td>{{ $transaksi->id }}</td>
                            <td>{{ $transaksi->nama }}</td>
                            <td>{{ $transaksi->alamat }}</td>
                            <td>{{ $transaksi->telepon }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y H:i') }}</td>
                            <td>
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->produk?->nama ?? 'Produk tidak ditemukan' }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->qty }}</div>
                                @endforeach
                            </td>
                            <td><span class="badge bg-primary text-uppercase">{{ $transaksi->metode }}</span></td>
                            <td class="fw-semibold text-success">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('admin.kelolastatuspesanan.update', $transaksi->id) }}" method="POST" class="d-flex justify-content-center align-items-center">
                                    @csrf
                                    @method('PUT')
                                    @php
                                        $statuses = ['pending', 'belum diproses', 'sedang diproses', 'selesai'];
                                    @endphp
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ $transaksi->status == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <form action="{{ route('admin.kelolastatuspesanan.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-hapus rounded-3">Hapus</button>
                                    </form>
                                    <form action="{{ route('admin.kelolastatuspesanan.kirimwa', $transaksi->id) }}" method="POST" onsubmit="return confirm('Kirim pesan WhatsApp ke {{ $transaksi->nama }}?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-3">Kirim WA</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-muted py-4">Belum ada data pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $transaksis->onEachSide(1)->links() }}
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
