<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            
            // KRITIS: Foreign keys untuk menghubungkan ke users dan categories
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Kreator
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null'); 
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Path gambar yang diunggah
            $table->json('tags')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};