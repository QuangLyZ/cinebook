<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    protected $fillable = ['movie_id', 'room_id', 'subtitle_id', 'start_time'];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function subtitle()
    {
        return $this->belongsTo(Subtitle::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
