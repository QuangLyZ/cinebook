<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoviePosterUpdateSeeder extends Seeder
{
    public function run(): void
    {
        // Sử dụng các URL từ TheMovieDB (TMDB) vì chúng cực kỳ ổn định và cho phép hotlinking tốt hơn
        $stablePosters = [
            'Avengers: Secret Wars' => 'https://image.tmdb.org/t/p/w500/uS9mY7ew9v7YUyLqaX6AavpY66S.jpg',
            'The Dark Knight' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDp9QmSbmz94S0OkiBh.jpg',
            'Spider-Man: No Way Home' => 'https://image.tmdb.org/t/p/w500/1g0zzY8pS6Sia9vO3ZkyG3v3Wub.jpg',
            'Dune: Part Two' => 'https://image.tmdb.org/t/p/w500/6iz9e9pU0mEovt0O03B9Wj62Sve.jpg',
        ];

        foreach ($stablePosters as $name => $url) {
            DB::table('movies')->where('name', $name)->update(['poster' => $url]);
        }
    }
}
