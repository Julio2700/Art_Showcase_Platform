<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    // 💡 TAMBAHKAN SEMUA KOLOM UNTUK MASS ASSIGNMENT
    protected $fillable = [
        'user_id', 
        'artwork_id', 
        'reason', 
        'status', 
    ];

    // Hubungan yang diperlukan
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function artwork(): BelongsTo 
    { 
        return $this->belongsTo(Artwork::class); 
    }
}