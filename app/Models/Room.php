<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
<<<<<<< HEAD
    protected $fillable = [
        'name',
        'cinema_id',
        'seat_count',
    ];

    public function cinema(): BelongsTo
=======
    protected $fillable = ['name', 'cinema_id', 'seat_count'];

    public function cinema()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->belongsTo(Cinema::class);
    }

<<<<<<< HEAD
    public function showtimes(): HasMany
=======
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->hasMany(Showtime::class);
    }
}
