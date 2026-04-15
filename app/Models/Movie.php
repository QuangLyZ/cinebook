<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $casts = [
        'release_date' => 'date',
    ];

    protected $fillable = [
        'name',
        'genre',
        'duration',
        'release_date',
        'director',
        'description',
        'poster',
        'actors',
        'age_limit',
        'trailer_link',
    ];

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }
}
