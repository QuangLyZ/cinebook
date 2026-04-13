<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'fullname' => 'Test User',
                'password' => bcrypt('password'),
                'phone' => '0987654321',
            ]
        );

        // Movie::truncate(); // PostgreSQL khong cho truncate neu co khoa ngoai

        Movie::updateOrCreate(
            ['name' => 'Avengers: Secret Wars'],
            [
                'genre' => 'Hành Động, Viễn Tưởng',
                'duration' => 150,
                'release_date' => now()->subDays(5),
                'director' => 'Joe Russo, Anthony Russo',
                'description' => 'Siêu anh hùng phải hợp lực để đối đầu với mối đe doạ xuyên vũ trụ.',
                'poster' => '/images/pic1.jpg',
                'actors' => 'Robert Downey Jr., Chris Evans, Mark Ruffalo',
                'age_limit' => 18,
                'trailer_link' => 'https://www.youtube.com/watch?v=example1',
            ]
        );

        Movie::updateOrCreate(
            ['name' => 'The Dark Knight'],
            [
                'genre' => 'Hành Động, Giật Gân',
                'duration' => 152,
                'release_date' => now()->subMonths(7),
                'director' => 'Christopher Nolan',
                'description' => 'Batman đối đầu với Joker trong một trận chiến tâm lý đầy kịch tính.',
                'poster' => '/images/batmanpic.jpg',
                'actors' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
                'age_limit' => 16,
                'trailer_link' => 'https://www.youtube.com/watch?v=example2',
            ]
        );

        Movie::updateOrCreate(
            ['name' => 'Spider-Man: No Way Home'],
            [
                'genre' => 'Hành Động, Phiêu Lưu',
                'duration' => 148,
                'release_date' => now()->subMonths(2),
                'director' => 'Jon Watts',
                'description' => 'Peter Parker phải đối mặt với hậu quả khi danh tính bị bại lộ.',
                'poster' => '/images/pic3nowayhome.jpg',
                'actors' => 'Tom Holland, Zendaya, Benedict Cumberbatch',
                'age_limit' => 13,
                'trailer_link' => 'https://www.youtube.com/watch?v=example3',
            ]
        );

        Movie::updateOrCreate(
            ['name' => 'Dune: Part Two'],
            [
                'genre' => 'Khoa Học Viễn Tưởng, Phiêu Lưu',
                'duration' => 165,
                'release_date' => now()->subWeeks(1),
                'director' => 'Denis Villeneuve',
                'description' => 'Hành trình của Paul Atreides tiếp tục trong sa mạc hành tinh Arrakis.',
                'poster' => '/images/pic2dune.jpg',
                'actors' => 'Timothée Chalamet, Zendaya, Rebecca Ferguson',
                'age_limit' => 18,
                'trailer_link' => 'https://www.youtube.com/watch?v=example4',
            ]
        );

        // Seed Cinemas
        $cinemas = [
            ['name' => 'CineBook Landmark 81', 'address' => 'Tầng B1, Vincom Center Landmark 81, 772 Điện Biên Phủ, P.22, Q.Bình Thạnh, TP.HCM'],
            ['name' => 'CineBook Aeon Tân Phú', 'address' => 'Tầng 3, Aeon Mall Tân Phú Celadon, 30 Bờ Bao Tân Thắng, P.Sơn Kỳ, Q.Tân Phú, TP.HCM'],
            ['name' => 'CineBook Sư Vạn Hạnh', 'address' => 'Tầng 6, Vạn Hạnh Mall, 11 Sư Vạn Hạnh, P.12, Q.10, TP.HCM'],
            ['name' => 'CineBook Giga Mall', 'address' => 'Tầng 6, Gigamall, 240-242 Phạm Văn Đồng, P.Hiệp Bình Chánh, Thủ Đức, TP.HCM'],
        ];

        foreach ($cinemas as $cinemaData) {
            \App\Models\Cinema::updateOrCreate(
                ['name' => $cinemaData['name']],
                ['address' => $cinemaData['address']]
            );
        }
    }
}
