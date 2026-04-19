<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->orderByDesc('publish_at')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.posts.index', [
            'posts' => $posts,
            'activeTab' => 'posts',
            'pageTitle' => 'Quản lý Bài Viết',
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
                'message' => 'Không thể tải ảnh lên Cloudinary. Kiểm tra lại cấu hình CLOUDINARY_* trong .env.',
            ], 422);
        }
    }

    private function uploadToCloudinary(?UploadedFile $file): string
    {
        if (! $file) {
            throw new \RuntimeException('No thumbnail file uploaded.');
        }

        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');
        $uploadPreset = (string) config('services.cloudinary.upload_preset');
        $folder = trim((string) config('services.cloudinary.folder', 'cinebook'), '/');

        if ($cloudName === '') {
            throw new \RuntimeException('Missing CLOUDINARY_CLOUD_NAME.');
        }

        $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
        $params = [];

        if ($folder !== '') {
            $params['folder'] = $folder.'/posts/thumbnails';
        }

        if ($uploadPreset !== '') {
            $params['upload_preset'] = $uploadPreset;
        } else {
            if ($apiKey === '' || $apiSecret === '') {
                throw new \RuntimeException('Missing CLOUDINARY_API_KEY or CLOUDINARY_API_SECRET.');
            }

            $timestamp = time();
            $signatureParams = $params;
            $signatureParams['timestamp'] = $timestamp;
            ksort($signatureParams);

            $signature = sha1(collect($signatureParams)
                ->map(fn ($value, $key) => $key.'='.$value)
                ->implode('&').$apiSecret);

            $params['api_key'] = $apiKey;
            $params['timestamp'] = $timestamp;
            $params['signature'] = $signature;
        }

        $request = Http::asMultipart()
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );

        foreach ($params as $key => $value) {
            $request = $request->attach($key, (string) $value);
        }

        $payload = $request->post($endpoint)->throw()->json();
        $url = $payload['secure_url'] ?? $payload['url'] ?? null;

        if (! is_string($url) || $url === '') {
            throw new \RuntimeException('Cloudinary did not return a usable URL.');
        }

        return $url;
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

}
