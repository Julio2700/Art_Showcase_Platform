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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curator_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            
            // PERBAIKAN: Menambahkan ->nullable() untuk mengatasi error MySQL 1067
            $table->timestamp('starts_at')->nullable(); 
            $table->timestamp('ends_at')->nullable(); 
            
            $table->string('banner_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
