<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Movie;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $movies = Movie::all();
        $users = User::all();

        if ($movies->isEmpty() || $users->isEmpty()) {
            return;
        }

        $comments = [
            'Phim hay quá sếp ơi, kịch bản quá đỉnh!',
            'Kỹ xảo mãn nhãn, rất đáng tiền vé.',
            'Cốt truyện hơi chậm nhưng ý nghĩa.',
            'Diễn xuất của diễn viên chính xuất sắc.',
            'Hơi thất vọng một chút về cái kết.',
            'Đỉnh cao điện ảnh, 10 điểm không có nhưng!',
            'Phim này xem với gấu là hết bài.',
            'Một trải nghiệm tuyệt vời tại CineBook.'
        ];

        foreach ($movies as $movie) {
            // Mỗi phim cho khoảng 2-4 review ngẫu nhiên
            $count = rand(2, 4);
            $randomUsers = $users->random(min($count, $users->count()));

            foreach ($randomUsers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'movie_id' => $movie->id,
                    'rating' => rand(4, 5), // Cho điểm cao tí cho đẹp
                    'comment' => $comments[array_rand($comments)],
                    'is_visible' => true,
                ]);
            }
        }
    }
}
