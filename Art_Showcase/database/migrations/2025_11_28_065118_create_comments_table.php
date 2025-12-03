<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // KOLOM KUNCI ASING YANG HILANG/BELUM TERSINKRONISASI:
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembuat komentar
            $table->foreignId('artwork_id')->constrained('artworks')->onDelete('cascade'); // Karya yang dikomentari
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};