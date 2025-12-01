<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    // Pastikan fillable Anda sudah sesuai dengan migrasi
    protected $fillable = [
        'challenge_id', 'artwork_id', 'is_winner', 'placement',
    ];

    // 💡 HUBUNGAN YANG HILANG: Submission belongs to a Challenge
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    // Hubungan lain yang diperlukan (Submission belongs to an Artwork)
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}