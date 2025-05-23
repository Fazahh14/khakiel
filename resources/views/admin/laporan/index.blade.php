@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Rekap Harian Transaksi</h2>

    {{-- Form Filter --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Tanggal Awal</label>
            <input type="date" name="start_date" id="start_date" class="form-control"
                value="{{ old('start_date', $startDate ?? '') }}" required>
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="form-control"
                value="{{ old('end_date', $endDate ?? '') }}" required>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
        </div>
    </form>

    {{-- Tabel Rekap --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal Pesanan</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($rekap['items']) && count($rekap['items']) > 0)
                    @foreach($rekap['items'] as $item)
                        <tr>
                            {{-- Format tanggal tanpa jam --}}
                            <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                            <td>{{ $item['nama_produk'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @elseif(!empty($startDate) && !empty($endDate))
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data transaksi pada filter yang dipilih.</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" class="text-center">Silakan pilih tanggal awal dan tanggal akhir terlebih dahulu.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Total Pendapatan --}}
    @if(isset($rekap['items']) && count($rekap['items']) > 0)
        <div class="mt-3">
            <h5>Total Pendapatan: 
                <strong>Rp {{ number_format($rekap['total_harga'], 0, ',', '.') }}</strong>
            </h5>
        </div>
    @endif

    {{-- Grafik Pendapatan --}}
    @if(!empty($grafikLabels) && !empty($grafikData))
        <div class="mt-5">
            <h3>Grafik Pendapatan</h3>
            <div style="overflow-x: auto;">
                <canvas id="grafikTanggalChart" height="300" style="min-width: 600px;"></canvas>
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
            const blue = Math.floor(150 + 105 * ratio);
            return `rgba(54, 162, ${blue}, 0.7)`;
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
                    borderColor: 'rgba(54, 162, 235, 1)',
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
