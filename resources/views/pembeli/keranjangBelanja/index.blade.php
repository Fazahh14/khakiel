@extends('layouts.pembeli')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    .input-pill-group button {
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
        font-weight: bold;
        user-select: none;
    }
    .input-pill-group span.jumlah {
        padding: 0 12px;
        display: inline-block;
        min-width: 30px;
        text-align: center;
    }
    .produk-card img {
        max-height: 80px;
        object-fit: contain;
    }
    .produk-link {
        text-decoration: none;
        color: inherit;
    }
    .produk-link:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-5 px-3 px-md-5">
    <h1 class="h4 fw-bold mb-4">Keranjang Belanja</h1>

    @if (count($keranjang) > 0)
    <form action="{{ route('checkout.storeProduk') }}" method="POST" id="form-keranjang">
        @csrf

        <div class="card card-custom mb-4">
            <div class="card-body d-flex align-items-center">
                <input type="checkbox" id="pilih-semua" class="form-check-input me-3">
                <label for="pilih-semua" class="form-check-label fw-semibold">
                    Pilih Semua (<span id="jumlah-dipilih">0</span> produk)
                </label>
            </div>
        </div>

        <div id="keranjang-list">
            @foreach ($keranjang as $item)
            <div class="card card-custom mb-4" data-id="{{ $item->id }}" data-stok="{{ $item->produk->stok ?? 0 }}">
                <div class="card-body d-flex produk-card align-items-center gap-3">
                    <input type="checkbox" name="produk[{{ $item->produk_id }}][check]" class="form-check-input item-checkbox" value="1">
                    
                    <a href="{{ route('pembeli.produk.show', $item->produk_id) }}" class="produk-link">
                        <img src="{{ $item->gambar ? asset('storage/' . $item->gambar) : asset('storage/default.png') }}" alt="{{ $item->nama }}" class="rounded" width="100" height="80">
                    </a>

                    <div class="flex-grow-1 position-relative">
                        <div class="position-absolute top-0 end-0 fw-bold">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </div>

                        <h5 class="fw-bold mb-2">
                            <a href="{{ route('pembeli.produk.show', $item->produk_id) }}" class="produk-link">{{ $item->nama }}</a>
                        </h5>

                        <div class="d-flex align-items-center gap-2">
                            <div class="input-pill-group">
                                <button type="button" class="btn btn-sm px-3" onclick="ubahJumlah('{{ $item->id }}', 'kurang')">−</button>
                                <span class="jumlah" id="jumlah-{{ $item->id }}">{{ $item->jumlah }}</span>
                                <button type="button" class="btn btn-sm px-3" onclick="ubahJumlah('{{ $item->id }}', 'tambah')">+</button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-2">
                            <button type="button" class="btn btn-link text-danger p-0" onclick="hapusProduk('{{ $item->id }}')">Hapus Produk</button>
                        </div>

                        <div class="mt-2 text-muted small">
                            Total: Rp <span class="subtotal">{{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</span>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="produk[{{ $item->produk_id }}][id]" value="{{ $item->produk_id }}">
                        <input type="hidden" name="produk[{{ $item->produk_id }}][nama]" value="{{ $item->nama }}">
                        <input type="hidden" name="produk[{{ $item->produk_id }}][harga]" value="{{ $item->harga }}">
                        <input type="hidden" name="produk[{{ $item->produk_id }}][jumlah]" class="input-jumlah produk-input" value="{{ $item->jumlah }}">
                        <input type="hidden" name="produk[{{ $item->produk_id }}][gambar]" value="{{ $item->gambar }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card card-custom">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="h5 mb-0 fw-semibold">
                    Total Semua: Rp <span id="total-harga">0</span>
                </div>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">Checkout Sekarang</button>
            </div>
        </div>
    </form>
    @else
    <div class="alert alert-info text-center">
        Keranjang belanja kamu kosong.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function ubahJumlah(id, aksi) {
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    const jumlahSpan = card.querySelector('.jumlah');
    const jumlahInput = card.querySelector('.input-jumlah');
    let jumlah = parseInt(jumlahSpan.innerText);
    const stok = parseInt(card.getAttribute('data-stok'));

    if (aksi === 'tambah') {
        if (jumlah < stok) {
            jumlah++;
        } else {
            alert('Jumlah tidak boleh melebihi stok yang tersedia!');
            return;
        }
    } else if (aksi === 'kurang' && jumlah > 1) {
        jumlah--;
    }

    jumlahSpan.innerText = jumlah;
    jumlahInput.value = jumlah;

    const hargaText = card.querySelector('.position-absolute.top-0.end-0').innerText.replace(/[^\d]/g, '');
    const harga = parseInt(hargaText);
    const subtotalElem = card.querySelector('.subtotal');
    subtotalElem.innerText = (harga * jumlah).toLocaleString('id-ID');

    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('#keranjang-list .card').forEach(card => {
        const checkbox = card.querySelector('.item-checkbox');
        if (checkbox && checkbox.checked) {
            const hargaText = card.querySelector('.position-absolute.top-0.end-0').innerText.replace(/[^\d]/g, '');
            const harga = parseInt(hargaText);
            const jumlah = parseInt(card.querySelector('.jumlah').innerText);
            total += harga * jumlah;
        }
    });
    document.getElementById('total-harga').innerText = total.toLocaleString('id-ID');
}

function updateJumlahDipilih() {
    const selected = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('jumlah-dipilih').innerText = selected;
}

function toggleProdukInputs(card, isChecked) {
    const inputs = card.querySelectorAll('.produk-input');
    inputs.forEach(input => input.disabled = !isChecked);
}

document.getElementById('pilih-semua').addEventListener('change', function () {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = this.checked;
        const card = cb.closest('.card');
        toggleProdukInputs(card, this.checked);
    });
    updateJumlahDipilih();
    updateTotal();
});

document.querySelectorAll('.item-checkbox').forEach(cb => {
    const card = cb.closest('.card');
    toggleProdukInputs(card, cb.checked);

    cb.addEventListener('change', () => {
        toggleProdukInputs(card, cb.checked);
        updateJumlahDipilih();
        updateTotal();

        if (!cb.checked) {
            document.getElementById('pilih-semua').checked = false;
        } else {
            const semua = document.querySelectorAll('.item-checkbox').length;
            const dipilih = document.querySelectorAll('.item-checkbox:checked').length;
            if (semua === dipilih) {
                document.getElementById('pilih-semua').checked = true;
            }
        }
    });
});

window.addEventListener('load', () => {
    updateJumlahDipilih();
    updateTotal();
});

function hapusProduk(id) {
    if (!confirm('Yakin ingin menghapus produk ini dari keranjang?')) return;

    fetch('{{ route("keranjang.hapusAjax") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-id="${id}"]`);
            if (card) card.remove();
            updateJumlahDipilih();
            updateTotal();

            if (document.querySelectorAll('#keranjang-list .card').length === 0) {
                location.reload();
            }

            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('Terjadi kesalahan saat menghapus produk.'));
}
</script>
@endpush
