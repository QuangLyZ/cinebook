<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Showtime extends Model
{
<<<<<<< HEAD
=======
    protected $fillable = ['movie_id', 'room_id', 'subtitle_id', 'start_time'];

>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    protected $casts = [
        'start_time' => 'datetime',
    ];

<<<<<<< HEAD
    protected $fillable = [
        'movie_id',
        'room_id',
        'subtitle_id',
        'start_time',
    ];

    public function movie(): BelongsTo
=======
    public function movie()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->belongsTo(Movie::class);
    }

<<<<<<< HEAD
    public function room(): BelongsTo
=======
    public function room()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->belongsTo(Room::class);
    }

<<<<<<< HEAD
    public function subtitle(): BelongsTo
    {
        return $this->belongsTo(Subtitle::class);
    }
=======
    public function subtitle()
    {
        return $this->belongsTo(Subtitle::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
}
