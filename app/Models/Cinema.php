<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cinema extends Model
{
<<<<<<< HEAD
    protected $fillable = [
        'name',
        'address',
    ];

    public function rooms(): HasMany
=======
    protected $fillable = ['name', 'address'];

    public function rooms()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->hasMany(Room::class);
    }
}
