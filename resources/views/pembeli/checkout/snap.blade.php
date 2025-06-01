<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Pembayaran</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
<script type="text/javascript">
    window.onload = function () {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function (result) {
                alert('Pembayaran berhasil!');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onPending: function (result) {
                alert('Pembayaran pending.');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onError: function (result) {
                alert('Pembayaran gagal!');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onClose: function () {
                alert('Transaksi dibatalkan oleh pengguna.');
                // Data tidak masuk database atau tetap pending karena transaksi tidak selesai
            }
        });
    }
</script>
</body>
</html>
