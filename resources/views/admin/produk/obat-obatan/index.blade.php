@extends('layouts.admin')

@section('title', 'Kelola Produk Obat-obatan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/produk/obatobatan/index.css') }}">
@endpush

@section('content')
<div class="container page-content">
    <div class="card">
        <h2 class="text-center mb-4 fw-bold text-uppercase text-dark">Kelola Produk Obat-obatan</h2>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        {{-- END FLASH MESSAGE --}}

        <div class="top-controls">
            <a href="{{ route('admin.obat-obatan.create') }}" class="btn-tambah">+ Tambah Produk</a>

            <form class="search-box" onsubmit="return false;">
                <input  type="text"
                        id="searchInput"
                        oninput="searchTable()"
                        class="search-input"
                        placeholder="Cari nama produk...">
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
                                <img src="{{ asset('storage/' . $p->gambar) }}" alt="gambar"
                                     style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            @else
                                <span class="text-muted fst-italic">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.obat-obatan.edit', $p->id) }}" class="btn btn-sm btn-edit">Edit</a>
                            <form  action="{{ route('admin.obat-obatan.destroy', $p->id) }}"
                                   method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
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
    const input   = document.getElementById('searchInput').value.toLowerCase();
    const tbody   = document.querySelector('#produkTable tbody');
    const rows    = tbody.querySelectorAll('tr:not(#emptyRow):not(#noResultRow)');
    let visible   = 0;

    rows.forEach(row => {
        const nama = row.cells[1].textContent.toLowerCase();
        if (nama.includes(input)) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    // baris default “Belum ada produk”
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.style.display = (visible === 0 && input === '') ? '' : 'none';

    // baris “Produk tidak ditemukan”
    let noRow = document.getElementById('noResultRow');
    if (visible === 0 && input !== '') {
        if (!noRow) {
            noRow = document.createElement('tr');
            noRow.id = 'noResultRow';
            noRow.innerHTML =
                `<td colspan="6" class="text-muted fst-italic">Produk tidak ditemukan.</td>`;
            tbody.appendChild(noRow);
        }
        noRow.style.display = '';
    } else if (noRow) {
        noRow.style.display = 'none';
    }
}
</script>
@endpush
@endsection
