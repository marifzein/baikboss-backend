<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('gender', ['male', 'female']);
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(1);
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['gender', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapists');
    }
};
