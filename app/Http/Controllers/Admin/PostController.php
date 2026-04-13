<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index()
    {
        try {
            $posts = DB::table('posts')
                ->join('Users as users', 'posts.author_id', '=', 'users.id')
                ->select('posts.*', 'users.fullname as author_name', 'users.email as author_email')
                ->latest('posts.created_at')
                ->paginate(10);
        } catch (\Throwable $e) {
            $posts = collect();
        }

        return view('admin.posts.index', [
            'posts' => $posts,
            'activeTab' => 'posts',
            'pageTitle' => 'Quản lý Bài Viết'
        ]);
    }
}
