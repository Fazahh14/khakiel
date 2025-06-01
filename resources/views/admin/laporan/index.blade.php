@extends('layouts.admin')

@section('content')
<div class="container py-4" style="background: #fff8e7; border-radius: 8px; box-shadow: 0 2px 8px rgba(122, 74, 0, 0.15);">
    <h2 class="mb-4" style="color: #7a4a00; font-weight: 700;">Rekap Harian Transaksi</h2>

    {{-- Form Filter --}}
    <form method="GET" class="row g-3 mb-4 p-3 rounded shadow-sm" style="background: #fff; border: 1px solid #d1bfa1;">
        <div class="col-md-3">
            <label for="start_date" class="form-label fw-semibold" style="color: #7a4a00;">Tanggal Awal</label>
            <input type="date" name="start_date" id="start_date" class="form-control border-2" style="border-color: #c99a4a;"
                value="{{ old('start_date', $startDate ?? '') }}" required>
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label fw-semibold" style="color: #7a4a00;">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="form-control border-2" style="border-color: #c99a4a;"
                value="{{ old('end_date', $endDate ?? '') }}" required>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn" style="background-color: #c99a4a; color: white; font-weight: 600; width: 100%; box-shadow: 0 2px 5px rgba(201, 154, 74, 0.5);">
                Tampilkan
            </button>
        </div>
    </form>

    {{-- Tabel Rekap --}}
    <div class="table-responsive shadow rounded" style="background: #fff; border: 1px solid #d1bfa1;">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead style="background-color: #c99a4a; color: white;">
                <tr>
                    <th>Tanggal Pesanan</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($rekap['items']) && count($rekap['items']) > 0)
                    @foreach($rekap['items'] as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                            <td>{{ $item['nama_produk'] }}</td>
                            <td class="text-center">{{ $item['qty'] }}</td>
                            <td class="text-end">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @elseif(!empty($startDate) && !empty($endDate))
                    <tr>
                        <td colspan="4" class="text-center text-muted fst-italic py-4">
                            Tidak ada data transaksi pada filter yang dipilih.
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" class="text-center text-muted fst-italic py-4">
                            Silakan pilih tanggal awal dan tanggal akhir terlebih dahulu.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Total Pendapatan --}}
    @if(isset($rekap['items']) && count($rekap['items']) > 0)
        <div class="mt-4 text-end">
            <h5>Total Pendapatan: 
                <span class="badge" style="background-color: #7a4a00; font-size: 1.25rem; color: white;">
                    Rp {{ number_format($rekap['total_harga'], 0, ',', '.') }}
                </span>
            </h5>
        </div>
    @endif

    {{-- Grafik Pendapatan --}}
    @if(!empty($grafikLabels) && !empty($grafikData))
        <div class="mt-5">
            <h3 style="color: #7a4a00; margin-bottom: 1rem;">Grafik Pendapatan</h3>
            <div style="overflow-x: auto;">
                <canvas id="grafikTanggalChart" height="320" style="min-width: 600px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(122, 74, 0, 0.2);"></canvas>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(!empty($grafikLabels) && !empty($grafikData))
        const labels = @json($grafikLabels);
        const data = @json($grafikData);
        const maxData = Math.max(...data);

        const bgColors = data.map(value => {
            const ratio = maxData > 0 ? value / maxData : 0;
            // Ganti ke warna coklat pastel, dari terang ke gelap
            const base = 122; // coklat rgb(122,74,0)
            const intensity = Math.floor(74 + (180 * ratio));
            return `rgba(${base}, ${74}, 0, 0.7)`; // rgba coklat gelap semi transparan
        });

        const ctx = document.getElementById('grafikTanggalChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: 'rgba(122, 74, 0, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    maxBarThickness: 40,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });
    @endif
});
</script>
@endpush
