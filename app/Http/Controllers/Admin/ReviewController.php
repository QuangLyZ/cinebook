<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'movie'])->latest()->paginate(20);
        
        $adminTabs = [
            'dashboard' => 'Dashboard',
            'management' => 'Quản lý',
            'posts' => 'Bài viết',
            'actions' => 'Action',
            'feedback' => 'Ý kiến phản hồi',
            'reviews' => 'Đánh giá phim'
        ];

        return view('admin.home', [
            'activeTab' => 'reviews',
            'pageTitle' => 'Quản lý Đánh giá',
            'adminTabs' => $adminTabs,
            'reviews' => $reviews
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
