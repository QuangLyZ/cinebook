<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subtitle extends Model
{
<<<<<<< HEAD
    protected $fillable = [
        'name',
    ];

    public function showtimes(): HasMany
=======
    protected $fillable = ['name'];

    public function showtimes()
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
    {
        return $this->hasMany(Showtime::class);
    }
}
