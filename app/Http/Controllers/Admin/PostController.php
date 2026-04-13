<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
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
        public function store(Request $request)
    {
        $request->validate([
        'title' => 'required',
        'content' => 'required',
        'publish_at' => 'required'
    ]);
      $post = Post::create([
        'title' => $request->title,
        'keywords' => $request->keywords,
        'content' => $request->content,
        'publish_at' => $request->publish_at,
        'status' => 'published'
    ]);


        $data['status'] = $request->publish_at ? 'scheduled' : 'published';
        
        Post::create($data);

        return redirect()->back()->with('success', 'Đăng bài thành công!');
    }
}
