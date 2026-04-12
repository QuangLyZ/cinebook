<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'discount_value',
        'discount_rate',
        'description',
        'is_active',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'discount_rate' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }
}
