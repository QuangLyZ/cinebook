<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $fillable = ['ticket_id', 'seat_id', 'price_at_booking'];

    protected $casts = [
        'price_at_booking' => 'decimal:2',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
