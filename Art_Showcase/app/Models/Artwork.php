<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artwork extends Model
{
    use HasFactory; // Sekarang Trait ini sudah dikenali

    protected $fillable = [
        // Kolom yang Anda tambahkan sebelumnya
        'user_id', 
        'category_id', 
        'title', 
        'description', 
        'file_path', 
        'tags', 
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array', 
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Hubungan lainnya yang digunakan di Controller Anda (untuk kelengkapan)
    public function category(): BelongsTo 
    { 
        return $this->belongsTo(Category::class); 
    }
    
    public function likes(): HasMany { return $this->hasMany(Like::class); }
    public function favorites(): HasMany { return $this->hasMany(Favorite::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }
    public function reports(): HasMany { return $this->hasMany(Report::class); }
    public function submissions(): HasMany { return $this->hasMany(Submission::class); }

}
