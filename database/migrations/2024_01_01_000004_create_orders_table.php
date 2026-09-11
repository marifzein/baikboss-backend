<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique();          // e.g. BB-20260911-XXXX

            // --- Klien ---
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('wa_number', 20);                     // denormalisasi no WA untuk akses cepat tim

            // --- Layanan ---
            $table->enum('service_type', ['rumah', 'kos', 'warung', 'kantor']);

            // --- Lokasi ASAL ("dari") ---
            $table->string('origin_label', 255);                 // alamat teks bebas dari GPS / input manual
            $table->decimal('origin_lat', 10, 7)->nullable();    // GPS (opsional)
            $table->decimal('origin_lng', 10, 7)->nullable();
            $table->foreignId('origin_kecamatan_id')->nullable()
                  ->constrained('tarif_kecamatan')->nullOnDelete(); // jika pilih "lokasi saya / kecamatan"

            // --- Lokasi TUJUAN ("ke") ---
            $table->enum('dest_type', ['bojonegoro', 'luar']);   // dalam Bojonegoro vs luar
            $table->foreignId('dest_tarif_id')->nullable()
                  ->constrained('tarif_kecamatan')->nullOnDelete(); // tujuan Bojonegoro -> baris tarif
            $table->foreignId('dest_wilayah_id')->nullable()
                  ->constrained('wilayah')->nullOnDelete();          // tujuan luar -> kecamatan di tabel wilayah
            $table->string('dest_label', 255);                   // alamat lengkap tujuan
            $table->decimal('dest_lat', 10, 7)->nullable();      // pin dari peta (opsional)
            $table->decimal('dest_lng', 10, 7)->nullable();

            // --- Jadwal ---
            $table->date('schedule_date');
            $table->time('schedule_time');

            // --- Biaya ---
            $table->unsignedBigInteger('tarif_amount')->default(0);  // tarif kecamatan/jarak
            $table->unsignedBigInteger('items_amount')->default(0);  // total biaya handling barang
            $table->unsignedBigInteger('total_amount')->default(0);

            $table->text('notes')->nullable();                   // catatan klien
            $table->string('status', 20)->default('pending');    // pending|paid|process|done|cancelled
            $table->timestamps();

            $table->index(['status', 'schedule_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
