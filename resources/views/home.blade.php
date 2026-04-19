@extends('layouts.app')
@section('content')
@php
    $heroMovie = $featuredMovie ?? null;
    $heroPoster = filled($heroMovie?->poster)
        ? $heroMovie->poster
        : 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2000&auto=format&fit=crop';
    $heroBookingUrl = filled($heroMovie?->booking_showtime_id)
        ? route('booking.show', $heroMovie->booking_showtime_id)
        : route('movies.index');
@endphp
<!-- Hero Section -->
<div class="relative bg-black text-white">
    <div class="absolute inset-0">
        <!-- Mock Hero Background -->
        <img class="w-full h-full object-cover opacity-50" src="{{ $heroPoster }}" alt="{{ $heroMovie?->name ?? 'Hero Movie' }}">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 flex flex-col justify-end min-h-[500px]">
        <h1 class="text-5xl font-extrabold tracking-tight mb-4 text-white">
            <span class="block">SIÊU PHẨM NỔI BẬT</span>
            <span class="block text-red-500">{{ $heroMovie?->name ?? 'AVENGERS: SECRET WARS' }}</span>
        </h1>
        <p class="mt-4 max-w-xl text-xl text-gray-300">
            {{ $heroMovie?->description ? \Illuminate\Support\Str::limit($heroMovie->description, 160) : 'Cuộc chiến cuối cùng định đoạt số phận của đa vũ trụ. Khám phá ngay siêu phẩm được mong chờ nhất thập kỷ tại CineBook!' }}
        </p>
        <div class="mt-8 flex gap-4">
            <a href="{{ $heroBookingUrl }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-red-500/30 flex items-center">
                <i class="fa-solid fa-ticket mr-2"></i> ĐẶT VÉ NGAY
            </a>
            @if (filled($heroMovie?->trailer_link))
                <a href="{{ $heroMovie->trailer_link }}" target="_blank" rel="noopener noreferrer" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors border border-gray-600 flex items-center">
                    <i class="fa-solid fa-play mr-2"></i> XEM TRAILER
                </a>
            @else
                <a href="https://www.youtube.com/watch?v=kH1XlwHQv9o" target="_blank" rel="noopener noreferrer" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors border border-gray-600 flex items-center">
                    <i class="fa-solid fa-play mr-2"></i> XEM TRAILER
                </a>
            @endif
        </div>
    </div>
</div>

@if (!empty($dbWarning))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <div class="rounded-xl border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-200">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ $dbWarning }}
    </div>
</div>
@endif

<!-- Phim Đang Chiếu -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white border-l-4 border-red-500 pl-3">Phim Đang Chiếu</h2>
            <p class="text-gray-400 mt-2">Đừng bỏ lỡ những bộ phim hot nhất tuần này</p>
        </div>
        <a href="/movies" class="text-red-500 hover:text-red-400 font-medium flex items-center">
            Xem tất cả <i class="fa-solid fa-chevron-right ml-1 text-sm"></i>
        </a>
    </div>

    <!-- Movie Carousel -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach ($nowShowing as $movie)
        <div class="group relative rounded-xl overflow-hidden bg-gray-800 transition-transform duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-red-500/20">
            <img src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=400&h=600&auto=format&fit=crop' }}" class="w-full h-80 object-cover" alt="{{ $movie->name }}">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent opacity-90"></div>
            
            <div class="absolute bottom-0 w-full p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="bg-red-600 outline outline-1 outline-white text-white text-xs font-bold px-2 py-0.5 rounded">{{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}</span>
                        <span class="text-yellow-500 text-xs flex items-center">
                            <i class="fa-solid fa-star mr-1"></i>
                            <span class="text-gray-300 font-bold">{{ $movie->average_rating }}</span>
                        </span>
                    </div>
                    <button onclick="openReviewModal({{ $movie->id }}, '{{ addslashes($movie->name) }}')" class="text-gray-400 hover:text-yellow-500 transition-colors" title="Đánh giá phim">
                        <i class="fa-regular fa-comment-dots text-lg"></i>
                    </button>
                </div>
                <h3 class="text-lg font-bold text-white mb-1 line-clamp-1">{{ $movie->name }}</h3>
                <p class="text-gray-400 text-sm mb-3">{{ $movie->genre }}</p>
                <div class="grid grid-cols-2 gap-2">
                    @if (filled($movie->booking_showtime_id))
                        <a href="{{ route('booking.show', $movie->booking_showtime_id) }}" class="text-center bg-red-600 hover:bg-red-700 text-white py-2 rounded font-medium transition-colors text-sm">
                            MUA VÉ
                        </a>
                    @else
                        <a href="{{ route('movies.index') }}" class="text-center bg-gray-700 hover:bg-red-600 text-white py-2 rounded font-medium transition-colors text-sm">
                            LỊCH CHIẾU
                        </a>
                    @endif
                    <button onclick="openReviewModal({{ $movie->id }}, '{{ addslashes($movie->name) }}')" class="text-center bg-gray-800 border border-gray-600 hover:bg-gray-700 text-white py-2 rounded font-medium transition-colors text-sm">
                        ĐÁNH GIÁ
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Phim Sắp Chiếu -->
<div class="bg-gray-800/50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-3xl font-bold text-white border-l-4 border-yellow-500 pl-3">Phim Sắp Chiếu</h2>
            <a href="/movies" class="text-yellow-500 hover:text-yellow-400 font-medium">Xem tất cả</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($comingSoon as $movie)
            <div class="group relative rounded-xl overflow-hidden bg-gray-800">
                <img src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1509281373149-e957c6296406?q=80&w=400&h=600&auto=format&fit=crop' }}" class="w-full h-80 object-cover opacity-80 group-hover:opacity-100 transition-opacity" alt="{{ $movie->name }}">
                <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-yellow-500 px-3 py-1 rounded-full text-sm font-bold border border-yellow-500/50">
                    {{ $movie->release_date ? \Carbon\Carbon::parse($movie->release_date)->format('d.m.Y') : 'Sắp ra mắt' }}
                </div>
                <div class="absolute bottom-0 w-full p-4 bg-gradient-to-t from-gray-900 to-transparent">
                    <h3 class="text-lg font-bold text-white mb-1">{{ $movie->name }}</h3>
                    <p class="text-gray-400 text-sm">{{ $movie->genre }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Tin Tức & Khuyến Mãi -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-white mb-8 border-l-4 border-blue-500 pl-3">Tin Tức & Khuyến Mãi</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse (($posts ?? collect()) as $post)
        <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-blue-500/50 transition-colors">
            <img src="https://images.unsplash.com/photo-1485001564903-56e6ca3736d8?q=80&w=600&h=300&auto=format&fit=crop" class="w-full h-48 object-cover" alt="News Image">
            <div class="p-6">
                <div class="text-blue-400 text-xs font-bold uppercase mb-2">
                    {{ $post->keywords ? \Illuminate\Support\Str::limit($post->keywords, 24) : 'Khuyến Mãi' }}
                </div>
                <h3 class="text-xl font-bold text-white mb-3 hover:text-blue-400 cursor-pointer">
                    {{ $post->title }}
                </h3>
                <p class="text-gray-400 text-sm line-clamp-3">
                    {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}
                </p>
                <div class="mt-4 text-gray-500 text-sm flex items-center">
                    <i class="fa-regular fa-calendar mr-2"></i> {{ optional($post->publish_at ?? $post->created_at)->format('d/m/Y') }}
                </div>
            </div>
        </div>
        @empty
        @for ($i = 1; $i <= 3; $i++)
        <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 hover:border-blue-500/50 transition-colors">
            <img src="https://images.unsplash.com/photo-1485001564903-56e6ca3736d8?q=80&w=600&h=300&auto=format&fit=crop" class="w-full h-48 object-cover" alt="News Image">
            <div class="p-6">
                <div class="text-blue-400 text-xs font-bold uppercase mb-2">Khuyến Mãi</div>
                <h3 class="text-xl font-bold text-white mb-3 hover:text-blue-400 cursor-pointer">Combo Bắp Nước Ưu Đãi 50% Dành Cho Sinh Viên</h3>
                <p class="text-gray-400 text-sm line-clamp-3">
                    Bắt đầu từ tháng này, CineBook mang đến ưu đãi cực khủng cho các bạn sinh viên. Chỉ cần mang thẻ sinh viên là có thể trải nghiệm...
                </p>
                <div class="mt-4 text-gray-500 text-sm flex items-center">
                    <i class="fa-regular fa-calendar mr-2"></i> {{ date('d/m/Y') }}
                </div>
            </div>
        </div>
        @endfor
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
// Logic openReviewModal đã được chuyển vào layouts/app.blade.php
</script>
@endpush
