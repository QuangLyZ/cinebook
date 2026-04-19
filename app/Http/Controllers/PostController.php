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

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // lưu file
        $path = $file->storeAs('uploads', $filename, 'public');

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
    $posts = Post::where('status', 'visible')
            ->orderBy('publish_at', 'desc')
            ->get();
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
        'publish_at' => 'nullable'
    ]);

    Post::create([
        'title' => $request->title,
        'keywords' => $request->keywords,
        'content' => $request->content,
        'thumbnail' => $request->thumbnail,
        'publish_at' => $request->publish_at,

        // 👉 QUAN TRỌNG: dùng visible
        'status' => 'visible'
    ]);

    return redirect()->back()->with('success', 'Đăng bài thành công!');
}
public function edit($id)
{
    $post = Post::findOrFail($id);
    $posts = Post::paginate(10); // thêm dòng này

    return view('admin.posts.edit', compact('post', 'posts'));
}

public function update(Request $request, $id)
{
    $post = Post::findOrFail($id);

    $post->update([
        'title' => $request->title,
        'keywords' => $request->keywords,
        'content' => $request->content,
        'thumbnail' => $request->thumbnail,
        'publish_at' => $request->publish_at,
    ]);

    return redirect()->route('admin.posts.index')
        ->with('success', 'Cập nhật thành công');
}
public function destroy($id)
{
    $post = Post::findOrFail($id);

    if ($post->thumbnail) {
        try {
            $publicId = pathinfo($post->thumbnail, PATHINFO_FILENAME);
            Cloudinary::destroy($publicId);
        } catch (\Exception $e) {}
    }

    $post->delete();

    return back()->with('success', 'Xóa bài viết thành công!');
}
public function show($id)
{
    $post = \App\Models\Post::findOrFail($id);
    return view('admin.posts.show', compact('post'));
}
public function toggle($id)
{
    $post = Post::findOrFail($id);

    if ($post->status == 'hidden') {
        $post->status = 'visible';
    } else {
        $post->status = 'hidden';
    }

    $post->save();

    return back()->with('success', 'Cập nhật thành công');
}
}