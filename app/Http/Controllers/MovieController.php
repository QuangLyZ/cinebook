<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Hiển thị trang chủ với danh sách phim
     */
    public function index()
    {
        // Lấy tất cả phim, sắp xếp mới nhất lên đầu
        $movies = Movie::orderBy('created_at', 'desc')->get();

        // Phân loại phim dựa trên ngày khởi chiếu (release_date)
        $today = now()->toDateString();

        // Phim đang chiếu: Đã phát hành
        $nowShowing = Movie::where('release_date', '<=', $today)->take(8)->get();

        // Phim sắp chiếu: Chưa phát hành
        $comingSoon = Movie::where('release_date', '>', $today)->take(8)->get();

        return view('home', compact('movies', 'nowShowing', 'comingSoon'));
    }

    /**
     * Hiển thị chi tiết phim và đặt vé
     */
    public function show($id)
    {
        // Sử dụng Movie model hiện tại
        $movie = Movie::findOrFail($id);
        return view('booking.show', compact('movie'));
    }

    /**
     * Trang danh sách phim
     */
    public function list()
    {
        $movies = Movie::all();
        $cinemas = \App\Models\Cinema::all();
        return view('movies.index', compact('movies', 'cinemas'));
    }
}
