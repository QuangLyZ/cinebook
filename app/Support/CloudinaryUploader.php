<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CloudinaryUploader
{
    public function uploadImage(UploadedFile $file, string $folderSuffix = ''): string
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');
        $uploadPreset = (string) config('services.cloudinary.upload_preset');
        $baseFolder = trim((string) config('services.cloudinary.folder', 'cinebook'), '/');

        if ($cloudName === '') {
            throw new \RuntimeException('Missing CLOUDINARY_CLOUD_NAME or CLOUDINARY_URL.');
        }

        $folder = trim($baseFolder.($folderSuffix !== '' ? '/'.trim($folderSuffix, '/') : ''), '/');
        $params = [];

        if ($folder !== '') {
            $params['folder'] = $folder;
        }

        if ($apiKey !== '' && $apiSecret !== '') {
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
        } elseif ($uploadPreset !== '') {
            $params['upload_preset'] = $uploadPreset;
        } else {
            throw new \RuntimeException('Missing CLOUDINARY credentials. Set CLOUDINARY_URL or CLOUDINARY_API_KEY/CLOUDINARY_API_SECRET.');
        }

        $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

        $response = Http::asMultipart()
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post($endpoint, $params);

        if ($response->failed()) {
            $message = data_get($response->json(), 'error.message')
                ?? data_get($response->json(), 'message')
                ?? Str::limit($response->body(), 300);

            throw new \RuntimeException('Cloudinary upload failed: '.$message);
        }

        $payload = $response->json();
        $url = $payload['secure_url'] ?? $payload['url'] ?? null;

        if (! is_string($url) || $url === '') {
            throw new \RuntimeException('Cloudinary did not return a usable URL.');
        }

        return $url;
    }
}
