<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function show(int $showtimeId): View
    {
        $showtime = null;

        try {
            $showtime = DB::table('Showtimes as showtimes')
                ->join('Movies as movies', 'movies.id', '=', 'showtimes.movie_id')
                ->join('Rooms as rooms', 'rooms.id', '=', 'showtimes.room_id')
                ->join('Cinemas as cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
                ->where('showtimes.id', $showtimeId)
                ->select([
                    'showtimes.id',
                    'showtimes.start_time',
                    'movies.name as movie_name',
                    'movies.poster',
                    'movies.age_limit',
                    'rooms.name as room_name',
                    'cinemas.name as cinema_name',
                ])
                ->first();
        } catch (QueryException) {
            // Render the fallback UI below when the schema is unavailable.
        }

        $startTime = $showtime?->start_time ? Carbon::parse($showtime->start_time) : null;

        return view('booking.show', [
            'showtime' => $showtime,
            'showDateLabel' => $startTime?->isToday()
                ? 'Hôm nay'
                : ($startTime ? ucfirst($startTime->translatedFormat('l, d/m/Y')) : 'Lịch chiếu đang cập nhật'),
            'showTimeLabel' => $startTime?->format('H:i') ?? '--:--',
            'posterUrl' => filled($showtime?->poster)
                ? $showtime->poster
                : 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=100&h=150&auto=format&fit=crop',
        ]);
    }
}
