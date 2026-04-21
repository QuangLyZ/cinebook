@extends('layouts.app')

@section('title', ($movie->name ?? 'Chi tiết phim') . ' - CineBook')

@section('content')
<div class="relative min-h-screen bg-gray-950 text-white overflow-hidden">
    <!-- Hero Backdrop Section -->
    <div class="absolute inset-0 w-full h-[70vh] z-0">
        <img src="{{ $movie->poster ? (str_starts_with($movie->poster, 'http') ? $movie->poster : asset(ltrim($movie->poster, '/'))) : 'https://via.placeholder.com/1920x1080' }}" 
             class="w-full h-full object-cover opacity-30 blur-xl scale-110" alt="backdrop">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/60 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 pt-12 pb-20">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm text-gray-400 font-medium">
            <a href="/" class="hover:text-red-500 transition-colors">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="/movies" class="hover:text-red-500 transition-colors">Phim</a>
            <span class="mx-2">/</span>
            <span class="text-gray-200">{{ $movie->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Left: Movie Poster -->
            <div class="w-full lg:w-1/3 flex-shrink-0">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-gray-900 rounded-2xl overflow-hidden shadow-2xl aspect-[2/3]">
                        <img src="{{ $movie->poster ? (str_starts_with($movie->poster, 'http') ? $movie->poster : asset(ltrim($movie->poster, '/'))) : 'https://via.placeholder.com/500x750' }}" 
                             class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105" 
                             alt="{{ $movie->name }}">
                        
                        <!-- Age Limit Badge -->
                        @if($movie->age_limit)
                            <div class="absolute top-4 right-4 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg border border-red-500/50">
                                {{ $movie->age_limit }}+
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons (Mobile) -->
                <div class="mt-8 flex flex-col gap-4">
                    <a href="#showtimes" 
                       class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-red-600/30 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-ticket-simple text-xl"></i>
                        <span>XEM LỊCH CHIẾU</span>
                    </a>
                    
                    <button onclick="openReviewModal({{ $movie->id }}, '{{ addslashes($movie->name) }}')"
                            class="w-full bg-gray-800/80 hover:bg-gray-700 border border-gray-700 text-white font-semibold py-4 rounded-xl transition-all flex items-center justify-center gap-3 backdrop-blur-md">
                        <i class="fa-regular fa-star text-xl text-yellow-500"></i>
                        <span>ĐÁNH GIÁ</span>
                    </button>
                </div>
            </div>

            <!-- Right: Movie Info -->
            <div class="w-full lg:w-2/3">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 tracking-tight leading-tight">
                    {{ $movie->name }}
                </h1>

                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 mb-8 text-sm md:text-base">
                    <div class="flex items-center text-yellow-500 bg-yellow-500/10 px-3 py-1.5 rounded-full border border-yellow-500/20">
                        <i class="fa-solid fa-star mr-2"></i>
                        <span class="font-bold">{{ number_format($averageRating, 1) }}</span>
                    </div>
                    <div class="flex items-center text-gray-300 bg-gray-800/50 px-3 py-1.5 rounded-full border border-gray-700/50">
                        <i class="fa-regular fa-clock mr-2 text-red-500"></i>
                        <span>{{ $movie->duration }} Phút</span>
                    </div>
                    <div class="flex items-center text-gray-300 bg-gray-800/50 px-3 py-1.5 rounded-full border border-gray-700/50">
                        <i class="fa-solid fa-layer-group mr-2 text-red-500"></i>
                        <span>{{ $movie->genre }}</span>
                    </div>
                    @if($movie->release_date)
                        <div class="flex items-center text-gray-300 bg-gray-800/50 px-3 py-1.5 rounded-full border border-gray-700/50">
                            <i class="fa-regular fa-calendar mr-2 text-red-500"></i>
                            <span>{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Synopsis -->
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                        <span class="w-1 h-6 bg-red-600 rounded-full mr-3"></span>
                        Nội dung phim
                    </h3>
                    <p class="text-gray-400 leading-relaxed text-lg italic">
                        {{ $movie->description ?? 'Chưa có mô tả cho bộ phim này.' }}
                    </p>
                </div>

                <!-- Trailer Section -->
                <div class="mb-12">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <span class="w-1 h-6 bg-red-600 rounded-full mr-3"></span>
                        Trailer chính thức
                    </h3>
                    @if($movie->trailer_link)
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-gray-800 group aspect-video">
                            @php
                                // Chuyển đổi link youtube thường sang link embed nếu cần
                                $trailerUrl = $movie->trailer_link;
                                if (str_contains($trailerUrl, 'watch?v=')) {
                                    $trailerUrl = str_replace('watch?v=', 'embed/', $trailerUrl);
                                } elseif (str_contains($trailerUrl, 'youtu.be/')) {
                                    $trailerUrl = str_replace('youtu.be/', 'youtube.com/embed/', $trailerUrl);
                                }
                            @endphp
                            <iframe src="{{ $trailerUrl }}" 
                                    class="absolute inset-0 w-full h-full" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="bg-gray-900/50 border border-dashed border-gray-700 rounded-2xl py-12 text-center text-gray-500">
                            <i class="fa-solid fa-video-slash text-4xl mb-3"></i>
                            <p>Trailer đang được cập nhật...</p>
                        </div>
                    @endif
                </div>

                <!-- Movie Cast/Crew -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div>
                        <h4 class="text-gray-500 uppercase text-xs font-bold tracking-widest mb-2">Đạo diễn</h4>
                        <p class="text-white font-medium">{{ $movie->director ?? 'Đang cập nhật' }}</p>
                    </div>
                    <div>
                        <h4 class="text-gray-500 uppercase text-xs font-bold tracking-widest mb-2">Diễn viên</h4>
                        <p class="text-white font-medium">{{ $movie->actors ?? 'Đang cập nhật' }}</p>
                    </div>
                </div>

                <!-- Showtimes Section -->
                <div id="showtimes" class="scroll-mt-12">
                    <h3 class="text-2xl font-bold text-white mb-8 flex items-center">
                        <i class="fa-solid fa-calendar-days text-red-600 mr-4"></i>
                        Lịch chiếu & Đặt vé
                    </h3>

                    @if($groupedShowtimes->isEmpty())
                        <div class="bg-gray-900/50 border border-gray-800 rounded-2xl py-10 text-center text-gray-500">
                            <p>Hiện chưa có suất chiếu cho phim này.</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach($groupedShowtimes as $date => $cinemas)
                                <div class="bg-gray-900/40 border border-gray-800 rounded-3xl p-6 md:p-8">
                                    <div class="flex items-center gap-4 mb-6 border-b border-gray-800 pb-4">
                                        <div class="bg-red-600/10 text-red-500 w-12 h-12 rounded-2xl flex flex-col items-center justify-center font-bold">
                                            <span class="text-xs leading-tight">{{ \Carbon\Carbon::parse($date)->translatedFormat('M') }}</span>
                                            <span class="text-xl leading-tight">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold text-lg">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d/m/Y') }}</h4>
                                            <p class="text-gray-500 text-sm">Chọn suất chiếu bạn muốn xem</p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        @foreach($cinemas as $cinemaName => $showtimes)
                                            <div class="group">
                                                <h5 class="text-gray-400 font-semibold mb-4 flex items-center group-hover:text-white transition-colors">
                                                    <i class="fa-solid fa-location-dot text-red-500 mr-3"></i>
                                                    {{ $cinemaName }}
                                                </h5>
                                                <div class="flex flex-wrap gap-3">
                                                    @foreach($showtimes as $showtime)
                                                        @php
                                                            $isPast = \Carbon\Carbon::parse($showtime->start_time)->isPast();
                                                        @endphp
                                                        <a href="{{ $isPast ? 'javascript:void(0)' : route('booking.show', $showtime->id) }}" 
                                                           class="group/time relative px-6 py-3 border rounded-xl transition-all duration-300
                                                           {{ $isPast 
                                                               ? 'bg-gray-900/50 border-gray-800 text-gray-600 cursor-not-allowed opacity-40 grayscale' 
                                                               : 'bg-gray-800 hover:bg-red-600 border-gray-700 hover:border-red-500 text-white' }}">
                                                            <div class="font-bold {{ !$isPast ? 'group-hover/time:scale-110' : '' }} transition-transform">
                                                                {{ \Carbon\Carbon::parse($showtime->start_time)->format('H:i') }}
                                                            </div>
                                                            <div class="text-[10px] uppercase mt-0.5 {{ $isPast ? 'text-gray-700' : 'text-gray-500 group-hover/time:text-red-100' }}">
                                                                {{ $showtime->subtitle->name ?? '2D' }}
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hiệu ứng fade in mượt mà */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .relative.z-10 {
        animation: fadeInUp 0.8s ease-out forwards;
    }
</style>
@endsection
