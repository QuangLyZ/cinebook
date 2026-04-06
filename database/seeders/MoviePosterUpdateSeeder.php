<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoviePosterUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $posters = [
            'Avengers: Secret Wars' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1000&auto=format&fit=crop', // Marvel Spider-Man (Chất lượng cao thay thế)
            'The Dark Knight' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?q=80&w=1000&auto=format&fit=crop', // Batman style
            'Spider-Man: No Way Home' => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1000&auto=format&fit=crop',
            'Dune: Part Two' => 'https://images.unsplash.com/photo-1506466010722-395aa2bef877?q=80&w=1000&auto=format&fit=crop', // Sci-fi desert
        ];

        // Link Poster chuẩn hơn từ các nguồn CDN ổn định
        $highResPosters = [
            'Avengers: Secret Wars' => 'https://w0.peakpx.com/wallpaper/137/1004/HD-wallpaper-avengers-secret-wars-2025.jpg',
            'The Dark Knight' => 'https://w0.peakpx.com/wallpaper/528/77/HD-wallpaper-batman-dark-knight-rises.jpg',
            'Spider-Man: No Way Home' => 'https://w0.peakpx.com/wallpaper/553/101/HD-wallpaper-spider-man-no-way-home-poster.jpg',
            'Dune: Part Two' => 'https://w0.peakpx.com/wallpaper/403/128/HD-wallpaper-dune-part-2-2023.jpg',
        ];

        foreach ($highResPosters as $name => $url) {
            DB::table('movies')->where('name', $name)->update(['poster' => $url]);
        }
    }
}
