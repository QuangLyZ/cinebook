<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'showtime_id',
        'payment_method',
        'status',
        'attempted_at',
        'amount',
        'discount_amount',
        'final_amount',
        'seat_count',
        'voucher_code',
        'reference_code',
        'customer_email',
        'customer_phone',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'seat_count' => 'integer',
            'metadata' => 'array',
        ];
    }
}
