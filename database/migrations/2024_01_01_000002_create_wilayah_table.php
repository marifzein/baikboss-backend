<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wilayah Indonesia untuk pilihan tujuan "luar Bojonegoro"
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['provinsi', 'kota', 'kecamatan'])->index();
            $table->string('nama', 100);
            $table->foreignId('parent_id')->nullable()->constrained('wilayah')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['level', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
