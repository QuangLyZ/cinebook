<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'showtime_id', 'fullname', 'email', 'phone',
        'booking_date', 'total_price'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_price'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function details()
    {
        return $this->hasMany(TicketDetail::class);
    }
}
