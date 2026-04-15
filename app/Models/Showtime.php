<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Showtime extends Model
{
    protected $casts = [
        'start_time' => 'datetime',
    ];

    protected $fillable = [
        'movie_id',
        'room_id',
        'subtitle_id',
        'start_time',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function subtitle(): BelongsTo
    {
        return $this->belongsTo(Subtitle::class);
    }
}
