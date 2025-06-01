@extends('layouts.admin')

@section('title', 'Kelola Status Pesanan')

<style>
    /* Styling tombol aksi */
    .btn-aksi {
        font-size: 0.85rem;
        padding: 0.45rem 0.75rem;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: 0.3s ease;
        user-select: none;
    }

    .btn-aksi i {
        font-size: 1rem;
    }

    /* Tombol Hapus - merah soft */
    .btn-hapus {
        background-color: #f08080;
        color: #fff;
    }

    .btn-hapus:hover {
        background-color: #e76e6e;
        transform: translateY(-2px);
    }

    /* Tombol Kirim WA - abu abu soft */
    .btn-wa {
        background-color: #9e9e9e; /* abu soft */
        color: #fff;
    }

    .btn-wa:hover {
        background-color: #7e7e7e;
        transform: translateY(-2px);
    }

    /* Select status */
    .form-select {
        min-width: 130px;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease;
        cursor: pointer;
    }

    .form-select:hover,
    .form-select:focus {
        border-color: #6da8f7;
        outline: none;
        box-shadow: 0 0 5px rgba(109, 168, 247, 0.5);
    }

    /* Badge style */
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
        border-radius: 0.4rem;
    }

    /* Table styling */
    .table-custom th {
        background-color: #f8f9fa;
        text-align: center;
        font-weight: 600;
        vertical-align: middle;
    }

    .table-custom td {
        vertical-align: middle;
        padding: 0.85rem;
        text-align: center;
    }

    .table-custom tr:hover {
        background-color: #f1f3f5;
    }

    /* Lebarkan kolom Total */
    .table-custom th:nth-child(9),
    .table-custom td:nth-child(9) {
        min-width: 180px;
        font-weight: 600;
        color: #020202; /* hijau teks total */
        text-align: center;
    }

    /* Wrapper tombol aksi */
    .aksi-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }

    /* Container padding */
    .page-content {
        padding-bottom: 2rem;
    }
</style>

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
            <table class="table table-hover table-custom">
                <thead>
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
                            <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_pesanan)->format('d-m-Y') }}</td>
                            <td>
                                @foreach($transaksi->items as $item)
                                    <div>{{ $item->produk?->nama ?? '-' }}</div>
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
                                <form action="{{ route('admin.kelolastatuspesanan.update', $transaksi->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select" onchange="this.form.submit()" required>
                                        <option value="sedang diproses" {{ $transaksi->status == 'sedang diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="selesai" {{ $transaksi->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div class="aksi-wrapper">
                                    <form action="{{ route('admin.kelolastatuspesanan.kirimwa', $transaksi->id) }}" method="POST" onsubmit="return confirm('Kirim pesan WhatsApp ke {{ $transaksi->nama }}?')">
                                        @csrf
                                        <button type="submit" class="btn-aksi btn-wa" title="Kirim WhatsApp">
                                            <i class="bi bi-whatsapp"></i> Kirim WA
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.kelolastatuspesanan.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-aksi btn-hapus" title="Hapus Pesanan">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </button>
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
