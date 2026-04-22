<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Support\CloudinaryUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    public function __construct(
        private readonly CloudinaryUploader $cloudinaryUploader
    ) {
    }

    public function index()
    {
        $movies = Movie::latest()->paginate(10);
        return view('admin.movies.index', [
            'movies' => $movies,
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Phim'
        ]);
    }

    public function create()
    {
        return view('admin.movies.create', [
            'activeTab' => 'management',
            'pageTitle' => 'Thêm Phim Mới'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->cloudinaryUploader->uploadImage(
                $request->file('poster'),
                'movies/posters'
            );
        }

        Movie::create($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', [
            'movie' => $movie,
            'activeTab' => 'management',
            'pageTitle' => 'Chỉnh Sửa Phim'
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->cloudinaryUploader->uploadImage(
                $request->file('poster'),
                'movies/posters'
            );
        }

        $movie->update($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy(Movie $movie)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($movie) {
                // 1. Xóa các đánh giá (Reviews) liên quan đến phim
                $movie->reviews()->delete();

                // 2. Xóa các dữ liệu liên quan đến suất chiếu (Showtimes)
                foreach ($movie->showtimes as $showtime) {
                    // Tìm các vé (Tickets) của suất chiếu này
                    $ticketIds = \Illuminate\Support\Facades\DB::table('tickets')
                        ->where('showtime_id', $showtime->id)
                        ->pluck('id');

                    if ($ticketIds->isNotEmpty()) {
                        // Xóa chi tiết vé (TicketDetails)
                        \Illuminate\Support\Facades\DB::table('ticket_details')
                            ->whereIn('ticket_id', $ticketIds)
                            ->delete();

                        // Xóa log thanh toán (PaymentLogs) nếu có
                        \Illuminate\Support\Facades\DB::table('payment_logs')
                            ->whereIn('ticket_id', $ticketIds)
                            ->orWhere('showtime_id', $showtime->id)
                            ->delete();

                        // Xóa các bản ghi vé (Tickets)
                        \Illuminate\Support\Facades\DB::table('tickets')
                            ->whereIn('id', $ticketIds)
                            ->delete();
                    }

                    // Xóa chính suất chiếu đó
                    $showtime->delete();
                }

                // 3. Cuối cùng mới xóa phim
                $movie->delete();
            });

            return redirect()->route('admin.movies.index')->with('success', 'Sếp đã xóa thành công phim và toàn bộ dữ liệu suất chiếu, vé liên quan rồi nhé! ✨');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi khi sếp xóa phim: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ôi sếp ơi, có lỗi khi xóa rồi: ' . $e->getMessage());
        }
    }
}
