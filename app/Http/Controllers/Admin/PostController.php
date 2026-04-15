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
            'title' => ['required', 'string', 'max:255'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'url', 'max:2048'],
            'publish_at' => ['nullable', 'date'],
        ]);

        Log::info('Admin post store payload received.', [
            'title' => $request->input('title'),
            'thumbnail' => $request->input('thumbnail'),
        ]);

        $publishAt = $request->filled('publish_at')
            ? Carbon::parse($request->input('publish_at'))
            : null;

        Post::create([
            'title' => $request->input('title'),
            'keywords' => $request->input('keywords'),
            'content' => $request->input('content'),
            'thumbnail' => $request->input('thumbnail'),
            'publish_at' => $publishAt,
            'status' => $publishAt && $publishAt->isFuture() ? 'scheduled' : 'published',
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đăng bài thành công!');
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
}
