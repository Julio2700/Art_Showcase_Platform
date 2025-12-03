<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{

    protected $fillable = [
        'user_id', 
        'artwork_id', 
        'content', 
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