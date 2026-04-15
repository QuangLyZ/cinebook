<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Cinema;
use App\Models\Subtitle;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Showtime::with(['movie', 'room.cinema', 'subtitle'])->latest('start_time');

        // Filters
        if ($request->filled('cinema_id')) {
            $query->whereHas('room', fn($q) => $q->where('cinema_id', $request->cinema_id));
        }
        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        $showtimes = $query->paginate(20)->withQueryString();
        $cinemas   = Cinema::orderBy('name')->get();
        $movies    = Movie::orderBy('name')->get();

        return view('admin.showtimes.index', [
            'showtimes' => $showtimes,
            'cinemas'   => $cinemas,
            'movies'    => $movies,
            'filters'   => $request->only(['cinema_id', 'movie_id', 'date']),
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Suất Chiếu',
        ]);
    }

    public function create()
    {
        $movies    = Movie::orderBy('name')->get();
        $cinemas   = Cinema::with('rooms')->orderBy('name')->get();
        $subtitles = Subtitle::orderBy('name')->get();

        return view('admin.showtimes.create', [
            'movies'    => $movies,
            'cinemas'   => $cinemas,
            'subtitles' => $subtitles,
            'activeTab' => 'management',
            'pageTitle' => 'Thêm Suất Chiếu',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id'    => 'required|exists:movies,id',
            'room_id'     => 'required|exists:rooms,id',
            'subtitle_id' => 'required|exists:subtitles,id',
            'start_time'  => 'required|date',
        ]);

        // Collision check: same room, overlapping time (movie duration)
        $movie    = Movie::findOrFail($validated['movie_id']);
        $duration = $movie->duration ?? 120; // default 120 mins
        $start    = \Carbon\Carbon::parse($validated['start_time']);
        $end      = $start->copy()->addMinutes($duration);

        $existingShowtimes = Showtime::with('movie')
            ->where('room_id', $validated['room_id'])
            ->whereDate('start_time', $start->toDateString())
            ->get();

        $conflict = false;
        foreach ($existingShowtimes as $existing) {
            $existingStart = \Carbon\Carbon::parse($existing->start_time);
            $existingDuration = $existing->movie->duration ?? 120;
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            // Check for overlap
            if ($start < $existingEnd && $end > $existingStart) {
                $conflict = true;
                break;
            }
        }

        if ($conflict) {
            return back()->withInput()->withErrors([
                'start_time' => 'Phòng chiếu đã có suất chiếu trong khung giờ này! Vui lòng chọn thời gian khác.',
            ]);
        }

        Showtime::create($validated);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Thêm suất chiếu thành công!');
    }

    public function show(Showtime $showtime)
    {
        $showtime->load(['movie', 'room.cinema', 'subtitle', 'tickets.user', 'tickets.details.seat']);
        return view('admin.showtimes.show', [
            'showtime'  => $showtime,
            'activeTab' => 'management',
            'pageTitle' => 'Chi tiết Suất Chiếu',
        ]);
    }

    public function edit(Showtime $showtime)
    {
        $movies    = Movie::orderBy('name')->get();
        $cinemas   = Cinema::with('rooms')->orderBy('name')->get();
        $subtitles = Subtitle::orderBy('name')->get();

        return view('admin.showtimes.edit', [
            'showtime'  => $showtime,
            'movies'    => $movies,
            'cinemas'   => $cinemas,
            'subtitles' => $subtitles,
            'activeTab' => 'management',
            'pageTitle' => 'Sửa Suất Chiếu',
        ]);
    }

    public function update(Request $request, Showtime $showtime)
    {
        $validated = $request->validate([
            'movie_id'    => 'required|exists:movies,id',
            'room_id'     => 'required|exists:rooms,id',
            'subtitle_id' => 'required|exists:subtitles,id',
            'start_time'  => 'required|date',
        ]);

        // Collision check (exclude current showtime)
        $movie    = Movie::findOrFail($validated['movie_id']);
        $duration = $movie->duration ?? 120;
        $start    = \Carbon\Carbon::parse($validated['start_time']);
        $end      = $start->copy()->addMinutes($duration);

        $existingShowtimes = Showtime::with('movie')
            ->where('room_id', $validated['room_id'])
            ->where('id', '!=', $showtime->id)
            ->whereDate('start_time', $start->toDateString())
            ->get();

        $conflict = false;
        foreach ($existingShowtimes as $existing) {
            $existingStart = \Carbon\Carbon::parse($existing->start_time);
            $existingDuration = $existing->movie->duration ?? 120;
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            // Check for overlap
            if ($start < $existingEnd && $end > $existingStart) {
                $conflict = true;
                break;
            }
        }

        if ($conflict) {
            return back()->withInput()->withErrors([
                'start_time' => 'Phòng chiếu đã có suất chiếu trong khung giờ này! Vui lòng chọn thời gian khác.',
            ]);
        }

        $showtime->update($validated);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Cập nhật suất chiếu thành công!');
    }

    public function destroy(Showtime $showtime)
    {
        $showtime->delete();
        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Xóa suất chiếu thành công!');
    }
}
