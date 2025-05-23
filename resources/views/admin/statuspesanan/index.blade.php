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
</style>
@endpush

@section('content')
<div class="container page-content">
    <div class="card p-4">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Kelola Status Pesanan</h2>

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
            <table class="table table-hover text-center align-middle">
                <thead class="table-light">

                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Tanggal Pesanan</th>
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
                            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y') }}</td>
                            <td>
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->produk->nama ?? 'Produk sudah dihapus' }}</div>
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
                                <form action="{{ route('admin.kelolastatuspesanan.update', $transaksi->id) }}" method="POST" class="d-flex justify-content-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        @foreach(['sedang diproses', 'selesai'] as $status)
                                            <option value="{{ $status }}" {{ $transaksi->status == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.kelolastatuspesanan.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                </form>

    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Telepon</th>
        <th>Tanggal Pesanan</th>
        <th>Nama Produk</th>
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

        <td>{{ ucfirst($transaksi->metode) }}</td> 

        <td>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
        <td>
            <form action="{{ route('admin.kelolastatuspesanan.update', $transaksi->id) }}" method="POST" class="d-flex justify-content-center align-items-center">
                @csrf
                @method('PUT')
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" required>
                    @php
                        $statuses = ['pending', 'belum diproses', 'sedang diproses', 'selesai'];
                    @endphp
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $transaksi->status == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </td>
        <td>
            <form action="{{ route('admin.kelolastatuspesanan.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
            </form>

            <form action="{{ route('admin.kelolastatuspesanan.kirimwa', $transaksi->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Kirim pesan WhatsApp ke {{ $transaksi->nama }}?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-success ms-1">Kirim WA</button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="11" class="text-muted">Belum ada data pesanan.</td>
    </tr>
    @endforelse
</tbody>


                                <form action="{{ route('admin.kelolastatuspesanan.kirimwa', $transaksi->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Kirim pesan WhatsApp ke {{ $transaksi->nama }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success ms-1">Kirim WA</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-muted">Belum ada data pesanan.</td></tr>
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
