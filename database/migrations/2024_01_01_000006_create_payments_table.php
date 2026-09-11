<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan: nama tabel "payment" (singular) sesuai spesifikasi
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->enum('method', ['transfer_bank'])->default('transfer_bank');
            $table->string('bank_name', 30)->nullable();         // BCA/BRI/Mandiri
            $table->string('account_number', 30)->nullable();
            $table->string('proof_path')->nullable();            // bukti transfer (opsional)
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
