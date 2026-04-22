<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\CloudinaryUploader;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct(
        private readonly CloudinaryUploader $cloudinaryUploader
    ) {
    }

    public function index()
    {
        return redirect()->route('admin.posts.create');
    }

    public function create()
    {
        return view('admin.posts.create', $this->buildPostPageData());
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

    public function uploadThumbnail(Request $request): JsonResponse
    {
        $request->validate([
            'thumbnail' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        try {
            $url = $this->uploadToCloudinary($request->file('thumbnail'));

            return response()->json([
                'url' => $url,
                'message' => 'Tải thumbnail lên Cloudinary thành công.',
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Cloudinary thumbnail upload failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => app()->isLocal()
                    ? $exception->getMessage()
                    : 'Không thể tải ảnh lên Cloudinary. Kiểm tra lại cấu hình CLOUDINARY_* trong .env.',
            ], 422);
        }
    }

    private function uploadToCloudinary(?UploadedFile $file): string
    {
        if (! $file) {
            throw new \RuntimeException('No thumbnail file uploaded.');
        }

        return $this->cloudinaryUploader->uploadImage($file, 'posts/thumbnails');
    }
public function edit($id)
{
    $post = Post::findOrFail($id);
    $posts = Post::query()
        ->orderByDesc('publish_at')
        ->orderByDesc('created_at')
        ->paginate(10);

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

    private function buildPostPageData(): array
    {
        return [
            'posts' => Post::query()
                ->orderByDesc('publish_at')
                ->orderByDesc('created_at')
                ->paginate(10),
            'activeTab' => 'posts',
            'pageTitle' => 'Quản lý Bài Viết',
        ];
    }

}
