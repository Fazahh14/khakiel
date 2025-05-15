@extends('layouts.admin')

@section('title', 'Kelola Produk Aksesoris')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/produk/aksesoris/index.css') }}">
@endpush

@section('content')
<div class="container page-content">
    <div class="card">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Kelola Produk Aksesoris</h2>

        <div class="top-controls">
            <a href="{{ route('admin.aksesoris.create') }}" class="btn-tambah">+ Tambah Produk</a>

            <form action="#" method="GET" class="search-box">
                <input type="text" id="searchInput" oninput="searchTable()" class="search-input" placeholder="Cari nama produk...">
                <i class="bi bi-search"></i>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover text-center" id="produkTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->stok }}</td>
                        <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td>
                            @if($p->gambar)
                                <img src="{{ asset('storage/' . $p->gambar) }}" alt="gambar" style="width: 64px; height: 64px; object-fit: cover; border-radius: 8px;">
                            @else
                                <span class="text-muted fst-italic">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.aksesoris.edit', $p->id) }}" class="btn btn-sm btn-edit">Edit</a>
                            <form action="{{ route('admin.aksesoris.destroy', $p->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-muted">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function searchTable() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const rows = document.querySelectorAll("#produkTable tbody tr");

        rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            row.style.display = nama.includes(input) ? "" : "none";
        });
    }
</script>
@endpush
@endsection
