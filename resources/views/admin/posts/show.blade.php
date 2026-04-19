@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 text-white">
    <div class="mt-10">
        <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-400 font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại trang chủ
        </a>
    </div>
    <h1 class="text-3xl font-bold mb-4">
        {{ $post->title }}
    </h1>

    <div class="text-gray-400 mb-6">
        {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}
    </div>
<div class="post-content max-w-none mb-4">
@php
    $content = $post->content;

    // convert youtube
    $content = preg_replace(
        '/<oembed url="https:\/\/youtu\.be\/(.*?)"><\/oembed>/',
        '<iframe width="100%" height="400" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
        $content
    );
$content = preg_replace(
    '/<p>\s*(<figure.*?>.*?<\/figure>)\s*<\/p>/is',
    '$1',
    $content
);
@endphp

    {!! $content !!}
</div>


    <div class="mt-10">
        <a href="{{ route('home') }}" class="text-blue-500 hover:text-blue-400 font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại trang chủ
        </a>
    </div>

</div>
@endsection
@section('scripts')
<style>
.post-content h1 {
    font-size: 36px !important;
    font-weight: 800 !important;
}
.post-content h2 {
    font-size: 28px !important;
    font-weight: 700 !important;
}
.post-content figure {
    text-align: center;
}
.post-content figure,
.post-content figure * {
    background: transparent !important;
}

.post-content img {
    display: block !important;
    margin: 0 auto !important;
    max-width: 100% !important;
    height: auto !important;
}
.post-content figure figcaption {
    text-align: center !important;
    font-style: normal !important;
}
.post-content img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    height: auto;

    /* cắt viền trắng */
    object-fit: cover;
}
.post-content figure.image {
    margin: 0 !important;
}

.post-content figure.image img {
    display: block !important;
    width: 100% !important;
    height: auto !important;
}
</style>
@endsection