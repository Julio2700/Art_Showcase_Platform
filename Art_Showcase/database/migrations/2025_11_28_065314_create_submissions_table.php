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
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('challenge_id')->constrained('challenges')->onDelete('cascade');
        $table->foreignId('artwork_id')->constrained('artworks')->onDelete('cascade');
        
        // 💡 KOLOM YANG HILANG/BELUM TERSINKRONISASI:
        $table->boolean('is_winner')->default(false); 
        $table->unsignedTinyInteger('placement')->nullable(); 
        
        $table->unique(['challenge_id', 'artwork_id']);
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
