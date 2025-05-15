@extends('layouts.admin')

@section('title', 'Kelola Status Pesanan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/statuspesanan/index.css') }}">
<style>
    .btn-simpan {
        background-color: #198754;
        color: white;
        border: none;
    }

    .btn-simpan:hover {
        background-color: #157347;
    }

    .btn-hapus {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-hapus:hover {
        background-color: #bb2d3b;
    }
</style>
@endpush

@section('content')
<div class="container page-content">
    <div class="card p-4">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Kelola Status Pesanan</h2>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover text-center align-middle" id="statusTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Tanggal Pesanan</th>
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
                        <td>{{ $transaksi->tanggal_pesanan }}</td>
                        <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('admin.kelolastatuspesanan.update', $transaksi->id) }}" method="POST" class="d-flex gap-2 justify-content-center align-items-center">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" required>
                                    <option value="pending" {{ $transaksi->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="belum diproses" {{ $transaksi->status == 'belum diproses' ? 'selected' : '' }}>Belum Diproses</option>
                                    <option value="sedang diproses" {{ $transaksi->status == 'sedang diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="selesai" {{ $transaksi->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('admin.kelolastatuspesanan.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-muted">Belum ada data pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
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
    document.addEventListener('DOMContentLoaded', function () {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            setTimeout(() => alert.remove(), 4000);
        }
    });
</script>
@endpush
