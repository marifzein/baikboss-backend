<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GlowController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// Catatan: prefix /api sudah otomatis ditambahkan Laravel untuk routes/api.php

// --- Auth ---
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth.token')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // --- Alamat tersimpan ---
    Route::get('addresses', [AddressController::class, 'index']);
    Route::post('addresses', [AddressController::class, 'store']);
    Route::put('addresses/{id}', [AddressController::class, 'update']);
    Route::delete('addresses/{id}', [AddressController::class, 'destroy']);

    // --- BossGlow ---
    Route::get('glow/services', [GlowController::class, 'services']);
    Route::get('glow/therapists', [GlowController::class, 'therapists']);
    Route::post('glow/bookings', [GlowController::class, 'store']);
    Route::get('glow/bookings', [GlowController::class, 'index']);
    Route::get('glow/bookings/{code}', [GlowController::class, 'show']);

    // --- Pesanan pindahan user login ---
    Route::get('my-orders', [OrderController::class, 'myOrders']);
});

// --- Master data (publik) ---
Route::get('layanan', [MasterDataController::class, 'layanan']);
Route::get('barang', [MasterDataController::class, 'barang']);
Route::get('tarif', [MasterDataController::class, 'tarif']);
Route::get('wilayah/provinsi', [MasterDataController::class, 'provinsi']);
Route::get('wilayah/kota/{provinsiId}', [MasterDataController::class, 'kota']);
Route::get('wilayah/kecamatan/{kotaId}', [MasterDataController::class, 'kecamatan']);
Route::get('rekening', [MasterDataController::class, 'rekening']);
Route::get('faq', [MasterDataController::class, 'faq']);

// --- Alur pesanan pindahan (akses via data diri; /my-orders versi login) ---
Route::post('quote', [OrderController::class, 'quote']);
Route::post('orders', [OrderController::class, 'store']);
Route::get('orders/{code}', [OrderController::class, 'show']);
Route::post('orders/{code}/pay', [OrderController::class, 'pay']);
