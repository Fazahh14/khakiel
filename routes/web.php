<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\RedirectIfNotBuyer;
use App\Http\Middleware\RedirectIfNotSeller;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\KelolaStatusPesananController;
use App\Http\Controllers\Admin\Produk\MakananKucingController;
use App\Http\Controllers\Admin\Produk\AksesorisController;
use App\Http\Controllers\Admin\Produk\ObatObatanController;
use App\Http\Controllers\Admin\Produk\PerlengkapanController;
use App\Http\Controllers\Admin\Produk\VitaminKucingController;
use App\Http\Controllers\Pembeli\ProdukController;
use App\Http\Controllers\Pembeli\KeranjangPembeliController;
use App\Http\Controllers\Pembeli\CheckoutController;
use App\Http\Controllers\Pembeli\BlogController;
use App\Http\Controllers\Pembeli\InformasiPesananController;
use App\Http\Controllers\Pembeli\WishlistController;

// =============================
// Upload Gambar CKEditor (Tanpa Middleware)
// =============================
Route::post('/admin/artikel/upload-gambar', [ArtikelController::class, 'uploadGambar'])->name('admin.artikel.upload.gambar');

// =============================
// Halaman Awal
// =============================
Route::get('/', fn () => redirect()->route('pembeli.produk.index'));

// =============================
// Autentikasi
// =============================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// =============================
// Produk (Publik / Guest & Auth)
// =============================
Route::get('/produk', [ProdukController::class, 'index'])->name('pembeli.produk.index');
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('pembeli.produk.show');

// =============================
// Blog (Publik - Tanpa Login)
// =============================
Route::get('/blog', [BlogController::class, 'index'])->name('pembeli.blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('pembeli.blog.show');

// =============================
// Pembeli (User Role: buyer)
// =============================
Route::middleware(['auth', RedirectIfNotBuyer::class])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('pembeli.produk.index'))->name('pembeli.dashboard');

    // Keranjang
    Route::prefix('keranjang')->name('keranjang.')->group(function () {
        Route::get('/', [KeranjangPembeliController::class, 'index'])->name('index');
        Route::post('/', [KeranjangPembeliController::class, 'store'])->name('store');
        Route::post('/tambah/{id}', [KeranjangPembeliController::class, 'tambah'])->name('tambah');
        Route::post('/kurang/{id}', [KeranjangPembeliController::class, 'kurang'])->name('kurang');
        Route::delete('/{id}', [KeranjangPembeliController::class, 'hapus'])->name('hapus'); // ✅ DELETE
        Route::post('/update', [KeranjangPembeliController::class, 'update'])->name('update');
    });

    // Checkout
    Route::post('/checkout/store-produk', [CheckoutController::class, 'storeProduk'])->name('checkout.storeProduk');
    Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout.form');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/midtrans/callback', [CheckoutController::class, 'callback']);

    // Informasi Pesanan
    Route::get('/informasi-pesanan', [InformasiPesananController::class, 'index'])->name('pembeli.informasipesanan.index');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/tambah', [WishlistController::class, 'tambah'])->name('wishlist.tambah');
    Route::post('/wishlist/hapus/{id}', [WishlistController::class, 'hapus'])->name('wishlist.hapus');
});

// =============================
// Admin / Seller
// =============================
Route::prefix('admin')->name('admin.')->middleware(['auth', RedirectIfNotSeller::class])->group(function () {
    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::resource('akun', AkunController::class)->except(['index']);

    Route::get('/laporan/penjualan', [LaporanController::class, 'index'])->name('laporan.penjualan');

    Route::get('/produk', fn () => view('admin.produk.index'))->name('produk.kelolaProduk');

    Route::resource('artikel', ArtikelController::class)->names('artikel');

    Route::resource('produk/makanan-kucing', MakananKucingController::class)->names('makanan-kucing');
    Route::resource('produk/aksesoris', AksesorisController::class)->names('aksesoris');
    Route::resource('produk/obat-obatan', ObatObatanController::class)->names('obat-obatan');
    Route::resource('produk/perlengkapan', PerlengkapanController::class)->names('perlengkapan');
    Route::resource('produk/vitamin-kucing', VitaminKucingController::class)->names('vitamin-kucing');

    Route::get('/status-pesanan', [KelolaStatusPesananController::class, 'index'])->name('kelolastatuspesanan.index');
    Route::put('/status-pesanan/{id}', [KelolaStatusPesananController::class, 'update'])->name('kelolastatuspesanan.update');
    Route::delete('/status-pesanan/{id}', [KelolaStatusPesananController::class, 'destroy'])->name('kelolastatuspesanan.destroy');
});

// =============================
// Fallback Route
// =============================
Route::fallback(fn () => redirect()->route('pembeli.produk.index'));
