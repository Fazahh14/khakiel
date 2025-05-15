<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
<script type="text/javascript">
    window.onload = function () {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function () {
                alert('Pembayaran berhasil!');
                @if($source === 'detail' && $produkId)
                    window.location.href = "{{ route('pembeli.produk.show', ['id' => $produkId]) }}";
                @else
                    window.location.href = "{{ route('pembeli.produk.index') }}";
                @endif
            },
            onPending: function () {
                alert('Menunggu pembayaran...');
                @if($source === 'detail' && $produkId)
                    window.location.href = "{{ route('pembeli.produk.show', ['id' => $produkId]) }}";
                @else
                    window.location.href = "{{ route('pembeli.produk.index') }}";
                @endif
            },
            onError: function () {
                alert('Pembayaran gagal!');
                @if($source === 'detail' && $produkId)
                    window.location.href = "{{ route('pembeli.produk.show', ['id' => $produkId]) }}";
                @else
                    window.location.href = "{{ route('pembeli.produk.index') }}";
                @endif
            },
            onClose: function () {
                alert('Transaksi dibatalkan.');
            }
        });
    }
</script>
</body>
</html>
