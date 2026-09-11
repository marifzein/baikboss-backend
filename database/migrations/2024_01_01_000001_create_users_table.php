<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);              // Nama klien
            $table->string('phone', 20)->unique();    // No WhatsApp (login ringan tanpa password)
            $table->string('email')->nullable();      // Opsional
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
