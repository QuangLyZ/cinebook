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
                'poster' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?q=80&w=600',
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
                'poster' => 'https://images.unsplash.com/photo-1517604931442-7f4f0f0fdcf3?q=80&w=600',
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
                'poster' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=600',
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
                'poster' => 'https://images.unsplash.com/photo-1518081461908-c3bd6158a59f?q=80&w=600',
                'actors' => 'Timothée Chalamet, Zendaya, Rebecca Ferguson',
                'age_limit' => 18,
                'trailer_link' => 'https://www.youtube.com/watch?v=example4',
            ]
        );
    }
}
