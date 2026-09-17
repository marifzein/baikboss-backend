<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();     // BG-20260916-XXXXXX

            // --- Layanan glow ---
            $table->enum('category', ['massage', 'facial']);
            $table->string('item_key', 40);                   // massage_male|massage_female|facial
            $table->string('item_label', 150);
            $table->unsignedTinyInteger('duration_hours')->default(1);
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('ongkir')->default(0);
            $table->unsignedBigInteger('total')->default(0);

            // --- Pemesan ---
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained('therapists')->nullOnDelete();
            $table->string('therapist_name', 100)->nullable(); // snapshot nama terapis

            // --- Alamat kunjungan ---
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->text('address_text');
            $table->decimal('address_lat', 10, 7)->nullable();
            $table->decimal('address_lng', 10, 7)->nullable();

            // --- Jadwal ---
            $table->date('schedule_date');
            $table->time('schedule_time');

            $table->text('notes')->nullable();                 // catatan pelanggan -> dishare ke terapis
            $table->string('status', 20)->default('pending');  // pending|confirmed|process|done|cancelled
            $table->timestamps();

            $table->index(['status', 'schedule_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
