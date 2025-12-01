<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // --- Kolom yang Hilang ---
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Pelapor
            $table->foreignId('artwork_id')->constrained('artworks')->onDelete('cascade'); // Karya yang dilaporkan
            $table->text('reason'); // Alasan laporan
            $table->string('status')->default('pending'); // Kolom 'status' yang dicari
            // -------------------------
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};