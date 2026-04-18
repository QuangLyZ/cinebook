<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Cinema;
use App\Models\Showtime;
use App\Models\Subtitle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RandomShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $movies = Movie::all();
        $cinemas = Cinema::with('rooms')->get();
        
        if ($movies->isEmpty() || $cinemas->isEmpty()) {
            return;
        }

        $subtitle = Subtitle::first() ?? Subtitle::create(['name' => 'Phụ Đề Tiếng Việt']);
        
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::today()->addDays($i);
        }

        foreach ($cinemas as $cinema) {
            $rooms = $cinema->rooms;
            if ($rooms->isEmpty()) continue;

            foreach ($dates as $date) {
                // Chọn ngẫu nhiên 3-4 phim cho mỗi rạp mỗi ngày
                $movieCount = min(4, $movies->count());
                $randomMovies = $movies->random($movieCount);

                foreach ($randomMovies as $movie) {
                    // Mỗi phim cho 3 suất chiếu ngẫu nhiên vào các khung giờ chính
                    $timeSlots = [
                        ['h' => rand(8, 11), 'm' => rand(0, 5) * 10],
                        ['h' => rand(13, 16), 'm' => rand(0, 5) * 10],
                        ['h' => rand(18, 22), 'm' => rand(0, 5) * 10],
                    ];

                    foreach ($timeSlots as $slot) {
                        $startTime = $date->copy()->setHour($slot['h'])->setMinute($slot['m']);
                        $room = $rooms->random();

                        Showtime::firstOrCreate([
                            'movie_id'    => $movie->id,
                            'room_id'     => $room->id,
                            'subtitle_id' => $subtitle->id,
                            'start_time'  => $startTime,
                        ]);
                    }
                }
            }
        }
    }
}
