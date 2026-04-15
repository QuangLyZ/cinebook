<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtitle extends Model
{
    protected $fillable = ['name'];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
