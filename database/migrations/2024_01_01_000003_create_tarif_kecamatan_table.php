<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan', 100)->unique(); // e.g. "Kedungadem"
            $table->string('kota', 100)->default('Bojonegoro');
            $table->string('provinsi', 100)->default('Jawa Timur');
            $table->unsignedBigInteger('tarif')->default(0);   // tarif dasar (Rp), di luar biaya item
            $table->unsignedBigInteger('tarif_luar')->nullable(); // opsional: tarif jika tujuan bukan titik pusat kecamatan
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tarif flat untuk luar Bojonegoro (diisi seeder, dapat diubah tanpa migrate)
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('tarif_kecamatan');
    }
};
