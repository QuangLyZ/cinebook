<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);

        foreach ($users as $user) {
            // Thể loại ưa thích
            $favoriteGenre = DB::table('tickets')
                ->join('showtimes', 'tickets.showtime_id', '=', 'showtimes.id')
                ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
                ->where('tickets.user_id', $user->id)
                ->select('movies.genre', DB::raw('count(*) as count'))
                ->whereNotNull('movies.genre')
                ->groupBy('movies.genre')
                ->orderByDesc('count')
                ->first();

            $user->favorite_genre = $favoriteGenre ? $favoriteGenre->genre : 'Chưa có dữ liệu';

            // Khung giờ hay xem
            $favoriteTime = DB::table('tickets')
                ->join('showtimes', 'tickets.showtime_id', '=', 'showtimes.id')
                ->where('tickets.user_id', $user->id)
                ->select(DB::raw('EXTRACT(HOUR FROM showtimes.start_time) as hour'), DB::raw('count(*) as count'))
                ->groupBy('hour')
                ->orderByDesc('count')
                ->first();

            if ($favoriteTime) {
                $h = (int) $favoriteTime->hour;
                if ($h >= 5 && $h < 12) {
                    $user->favorite_time = 'Sáng (' . sprintf('%02d:00', $h) . ')';
                } elseif ($h >= 12 && $h < 18) {
                    $user->favorite_time = 'Chiều (' . sprintf('%02d:00', $h) . ')';
                } else {
                    $user->favorite_time = 'Tối (' . sprintf('%02d:00', $h) . ')';
                }
            } else {
                $user->favorite_time = 'Chưa có dữ liệu';
            }
            
            // Tổng chi tiêu
            $totalSpent = DB::table('tickets')
                ->where('user_id', $user->id)
                ->sum('total_price');
            $user->total_spent = $totalSpent;
        }

        return view('admin.users.index', [
            'users' => $users,
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Khách Hàng'
        ]);
    }
}
