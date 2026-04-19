<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    protected $casts = [
        'publish_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'keywords',
        'title',
        'content',
        'thumbnail',
        'publish_at',
        'status',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
             ->where('status', 'visible')
            ->where(function (Builder $builder) {
                $builder->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            });
    }
}
