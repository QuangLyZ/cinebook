<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoviePosterUpdateSeeder extends Seeder
{
    public function run(): void
    {
        // Sử dụng các ảnh Sếp vừa cung cấp từ folder public/images
        $localPosters = [
            'Avengers: Secret Wars' => '/images/pic1.jpg',
            'The Dark Knight' => '/images/batmanpic.jpg',
            'Spider-Man: No Way Home' => '/images/pic3nowayhome.jpg',
            'Dune: Part Two' => '/images/pic2dune.jpg',
        ];

        foreach ($localPosters as $name => $url) {
            DB::table('movies')->where('name', $name)->update(['poster' => $url]);
        }
    }
}
