<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    // 💡 TAMBAHKAN user_id dan artwork_id ke fillable
    protected $fillable = [
        'user_id', 
        'artwork_id', 
    ];

    // Hubungan (Opsional, tapi penting untuk query)
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function artwork(): BelongsTo 
    { 
        return $this->belongsTo(Artwork::class); 
    }
}
