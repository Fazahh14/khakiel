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
            onSuccess: function () {
                alert('Pembayaran berhasil!');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onPending: function () {
                alert('Pembayaran pending.');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onError: function () {
                alert('Pembayaran gagal!');
                window.location.href = "{{ route('pembeli.produk.index') }}";
            },
            onClose: function () {
                alert('Transaksi dibatalkan oleh pengguna.');
            }
        });
    }
</script>
</body>
</html>
