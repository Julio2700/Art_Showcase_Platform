<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            
            // KRITIS: Tambahkan foreign keys user_id dan artwork_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artwork_id')->constrained('artworks')->onDelete('cascade');
            
            // Memastikan satu user hanya bisa menambahkan satu karya sekali ke favorite
            $table->unique(['user_id', 'artwork_id']); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};