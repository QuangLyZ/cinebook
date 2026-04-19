<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Movie $movie)
    {
        $reviews = $movie->reviews()
            ->where('is_visible', true)
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'average_rating' => $movie->average_rating,
            'review_count' => $movie->review_count,
            'reviews' => $reviews
        ]);
    }

    public function store(Request $request, Movie $movie)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Vui lòng đăng nhập để đánh giá!'], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Kiểm tra xem user đã đánh giá phim này chưa
        $existingReview = Review::where('user_id', Auth::id())
            ->where('movie_id', $movie->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Cập nhật đánh giá thành công!';
        } else {
            Review::create([
                'user_id' => Auth::id(),
                'movie_id' => $movie->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Đánh giá thành công! Cảm ơn sếp.';
        }

        return response()->json([
            'message' => $message,
            'average_rating' => $movie->average_rating,
            'review_count' => $movie->review_count,
        ]);
    }

    public function destroy(Review $review)
    {
        if (!Auth::user()->admin_role && Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Hành động không được phép!'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Đã xóa đánh giá thành công.']);
    }
}
