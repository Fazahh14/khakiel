@extends('layouts.admin')

@section('title', 'Kelola Artikel')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/artikel/index.css') }}">
@endpush

@section('content')
<div class="container page-content">
    {{-- Header --}}
    <div class="card">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Kelola Artikel</h2>

        <div class="top-controls">
            <a href="{{ route('admin.artikel.create') }}" class="btn-tambah">+ Tambah Artikel</a>

            <form action="#" method="GET" class="search-box">
                <input type="text" id="searchInput" oninput="searchTable()" class="search-input" placeholder="Cari judul artikel...">
                <i class="bi bi-search"></i>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover text-center" id="artikelTable">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($artikel as $a)
                        <tr>
                            <td>{{ $a->judul }}</td>
                            <td>{{ $a->created_at->format('d M Y') }}</td>
                            <td>
                                @if($a->gambar)
                                    <img src="{{ asset('storage/' . $a->gambar) }}" alt="gambar" class="thumbnail-img">
                                @else
                                    <span class="text-muted fst-italic">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.artikel.edit', $a->id) }}" class="btn btn-sm btn-edit">Edit</a>
                                    <form action="{{ route('admin.artikel.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Belum ada artikel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function searchTable() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const rows = document.querySelectorAll("#artikelTable tbody tr");

        rows.forEach(row => {
            const judul = row.cells[0].textContent.toLowerCase();
            row.style.display = judul.includes(input) ? "" : "none";
        });
    }
</script>
@endpush
