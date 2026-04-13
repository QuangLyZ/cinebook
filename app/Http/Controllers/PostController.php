<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class PostController extends Controller
{   
public function uploadImage(Request $request)
{
    if ($request->hasFile('upload')) {

        $file = $request->file('upload');

        $request->validate([
            'upload' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // đổi tên file (tránh lỗi tiếng Việt)
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // lưu file
        $path = $file->storeAs('uploads', $filename, 'public');

        // ✅ QUAN TRỌNG: format đúng cho CKEditor
        return response()->json([
            "uploaded" => 1,
            "fileName" => $filename,
            "url" => asset('storage/' . $path)
        ]);
    }

    return response()->json([
        "uploaded" => 0,
        "error" => [
            "message" => "Upload failed"
        ]
    ]);
}
    public function list()
    {
    $posts = Post::orderBy('publish_at', 'desc')->get();
    return view('post.index', compact('posts'));
    }
    public function detail($slug)
    {
    $post = Post::where('slug', $slug)->firstOrFail();
    return view('post.detail', compact('post'));
    }
    public function index()
    {
        $posts = Post::latest()->get();

        return view('admin.home', [
            'activeTab' => 'posts',
            'pageTitle' => 'Bài viết',
            'posts' => $posts
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