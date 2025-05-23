@extends('layouts.admin')

@section('title', 'Kelola Akun Pembeli')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/akun/index.css') }}">
@endpush

@section('content')
<div class="container page-content">
    <div class="card animate-fade-slide">

        {{-- Judul di Tengah --}}
        <h2 class="text-center fw-bold text-uppercase mb-4">Kelola Akun</h2>

        {{-- Tombol dan Form Pencarian --}}
        <div class="top-controls">
            <a href="{{ route('admin.akun.create') }}" class="btn-tambah">+ Tambah Akun</a>

            <form class="search-box" onsubmit="return false;">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama/email..." oninput="searchTable()">
                <i class="bi bi-search"></i>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="table-responsive animate-fade-slide">
            <table class="table table-hover text-center" id="akunTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($akuns as $akun)
                        <tr>
                            <td>{{ $akun->id }}</td>
                            <td>{{ $akun->name }}</td>
                            <td>{{ $akun->email }}</td>
                            <td>{{ $akun->role == 'seller' ? 'Admin' : 'Pembeli' }}</td>
                            <td>
                                <a href="{{ route('admin.akun.edit', $akun->id) }}" class="btn btn-sm btn-edit">Edit</a>
                                <form action="{{ route('admin.akun.destroy', $akun->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus akun ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="5">Tidak ada akun ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $akuns->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function searchTable() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const tableBody = document.querySelector("#akunTable tbody");
        const rows = tableBody.querySelectorAll("tr:not(#emptyRow):not(#noResultRow)");

        let visibleCount = 0;

        rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            if (nama.includes(input) || email.includes(input)) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        const emptyRow = document.getElementById("emptyRow");
        if (emptyRow) {
            emptyRow.style.display = (visibleCount === 0 && input === '') ? "" : "none";
        }

        let noResultRow = document.getElementById("noResultRow");
        if (visibleCount === 0 && input !== '') {
            if (!noResultRow) {
                noResultRow = document.createElement("tr");
                noResultRow.id = "noResultRow";
                noResultRow.innerHTML = `<td colspan="5" class="text-muted fst-italic">Akun tidak ditemukan.</td>`;
                tableBody.appendChild(noResultRow);
            }
            noResultRow.style.display = "";
        } else {
            if (noResultRow) {
                noResultRow.style.display = "none";
            }
        }
    }
</script>
@endpush
