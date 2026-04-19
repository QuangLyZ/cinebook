<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'showtime_id',
        'fullname',
        'email',
        'phone',
        'booking_date',
        'total_price',
        'discount_amount',
        'final_price',
        'voucher_code',
        'reference_code',
        'emailed_at'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'emailed_at' => 'datetime',
        'reminded_at' => 'datetime',
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
