<?php

use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// Catatan: prefix /api sudah otomatis ditambahkan Laravel untuk routes/api.php
Route::get('layanan', [MasterDataController::class, 'layanan']);
Route::get('barang', [MasterDataController::class, 'barang']);
Route::get('tarif', [MasterDataController::class, 'tarif']);
Route::get('wilayah/provinsi', [MasterDataController::class, 'provinsi']);
Route::get('wilayah/kota/{provinsiId}', [MasterDataController::class, 'kota']);
Route::get('wilayah/kecamatan/{kotaId}', [MasterDataController::class, 'kecamatan']);
Route::get('rekening', [MasterDataController::class, 'rekening']);
Route::get('faq', [MasterDataController::class, 'faq']);

// Alur pesanan
Route::post('quote', [OrderController::class, 'quote']);
Route::post('orders', [OrderController::class, 'store']);
Route::get('orders/{code}', [OrderController::class, 'show']);
Route::post('orders/{code}/pay', [OrderController::class, 'pay']);
