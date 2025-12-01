<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'curator_id', 'title', 'description', 'banner_path', 'starts_at', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
    
    // Hubungan (Relationships)
    public function curator()
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}