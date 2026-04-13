<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CinemaController extends Controller
{
    public function index(): View
    {
        $cinemas = collect();

        try {
            $cinemas = \App\Models\Cinema::select('id', 'name', 'address')
                ->orderBy('name')
                ->get();
        } catch (QueryException) {
            // Keep the page renderable when the schema has not been imported yet.
        }

        return view('cinemas.index', [
            'cinemas' => $cinemas,
        ]);
    }

    public function show(Request $request, int $cinemaId): View
    {
        $cinema = null;
        $availableDates = collect();
        $movies = collect();
        $selectedDate = Carbon::today()->toDateString();

        try {
            $cinema = DB::table('cinemas')
                ->select('id', 'name', 'address')
                ->where('id', $cinemaId)
                ->first();

            abort_if(! $cinema, 404);

            $availableDates = DB::table('showtimes as showtimes')
                ->join('rooms as rooms', 'rooms.id', '=', 'showtimes.room_id')
                ->where('rooms.cinema_id', $cinemaId)
                ->where('showtimes.start_time', '>=', Carbon::today()->startOfDay())
                ->selectRaw('DATE(showtimes.start_time) as show_date')
                ->distinct()
                ->orderBy('show_date')
                ->limit(7)
                ->get()
                ->pluck('show_date')
                ->map(fn ($date) => Carbon::parse($date));

            if ($availableDates->isEmpty()) {
                $availableDates = collect(range(0, 6))
                    ->map(fn ($offset) => Carbon::today()->copy()->addDays($offset));
            }

            $requestedDate = $request->query('date');
            $selectedDate = $this->resolveSelectedDate($requestedDate, $availableDates);

            $showtimes = DB::table('showtimes as showtimes')
                ->join('rooms as rooms', 'rooms.id', '=', 'showtimes.room_id')
                ->join('movies as movies', 'movies.id', '=', 'showtimes.movie_id')
                ->leftJoin('subtitles as subtitles', 'subtitles.id', '=', 'showtimes.subtitle_id')
                ->where('rooms.cinema_id', $cinemaId)
                ->whereDate('showtimes.start_time', $selectedDate)
                ->orderBy('showtimes.start_time')
                ->select([
                    'movies.id as movie_id',
                    'movies.name as movie_name',
                    'movies.poster',
                    'movies.description',
                    'movies.genre',
                    'movies.duration',
                    'movies.age_limit',
                    'rooms.name as room_name',
                    'showtimes.id as showtime_id',
                    'showtimes.start_time',
                    'subtitles.name as subtitle_name',
                ])
                ->get();

            $movies = $showtimes
                ->groupBy('movie_id')
                ->map(function (Collection $movieShowtimes) {
                    $first = $movieShowtimes->first();

                    return (object) [
                        'id' => $first->movie_id,
                        'name' => $first->movie_name,
                        'poster' => $first->poster,
                        'description' => $first->description,
                        'genre' => $first->genre,
                        'duration' => $first->duration,
                        'age_limit' => $first->age_limit,
                        'showtimes' => $movieShowtimes->map(function ($showtime) {
                            $startTime = Carbon::parse($showtime->start_time);

                            return (object) [
                                'id' => $showtime->showtime_id,
                                'time' => $startTime->format('H:i'),
                                'date_label' => $startTime->translatedFormat('d/m/Y'),
                                'room_name' => $showtime->room_name,
                                'subtitle_name' => $showtime->subtitle_name,
                            ];
                        }),
                    ];
                })
                ->values();
        } catch (QueryException) {
            abort_if(! $cinema, 404);
        }

        return view('cinemas.show', [
            'cinema' => $cinema,
            'movies' => $movies,
            'selectedDate' => $selectedDate,
            'availableDates' => $availableDates,
        ]);
    }

    private function resolveSelectedDate(?string $requestedDate, Collection $availableDates): string
    {
        if ($requestedDate) {
            try {
                $parsedDate = Carbon::parse($requestedDate)->toDateString();

                if ($availableDates->contains(fn (Carbon $date) => $date->toDateString() === $parsedDate)) {
                    return $parsedDate;
                }
            } catch (\Throwable) {
                // Fall back to the first available date below.
            }
        }

        return $availableDates->first()->toDateString();
    }
}
