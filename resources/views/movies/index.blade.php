@extends('layouts.app')

@section('content')
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Đang Chiếu Tại Rạp</h1>

        <div class="flex flex-col md:flex-row gap-4 bg-gray-800 p-4 rounded-xl border border-gray-700">
            <div class="flex-1 relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input
                    type="text"
                    id="movieSearch"
                    placeholder="Tìm tên phim, thể loại..."
                    onkeyup="filterMovies()"
                    class="block w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none"
                >
            </div>
            <select
                id="genreFilter"
                onchange="filterMovies()"
                class="bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-1 focus:ring-red-500 outline-none"
            >
                <option value="">Tất cả thể loại</option>
                <option value="Hành Động">Hành Động</option>
                <option value="Hài Hước">Hài Hước</option>
                <option value="Kinh Dị">Kinh Dị</option>
                <option value="Tình Cảm">Tình Cảm</option>
                <option value="Hoạt Hình">Hoạt Hình</option>
                <option value="Viễn Tưởng">Viễn Tưởng</option>
            </select>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- ===== SIDEBAR RẠP (từ DB) ===== --}}
        <div class="md:col-span-1 space-y-4">
            <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2">
                <i class="fa-solid fa-building text-red-500 mr-2"></i>Chọn Rạp
            </h3>

            <div class="space-y-2" id="cinemaList">
                @forelse($cinemas ?? [] as $index => $cinema)
                <a
                    href="{{ route('cinemas.show', $cinema->id) }}"
                    class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors block
                        {{ $index === 0 ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' }}"
                >
                    <span class="font-medium text-sm">{{ $cinema->name }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                @empty
                {{-- Fallback nếu chưa có DB --}}
                @foreach(['CineBook Landmark 81', 'CineBook Sư Vạn Hạnh', 'CineBook Gò Vấp', 'CineBook Quận 7', 'CineBook Thủ Đức'] as $i => $name)
                <a
                    href="{{ route('cinemas.index') }}"
                    class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors block
                        {{ $i === 0 ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700' }}"
                >
                    <span class="font-medium text-sm">{{ $name }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                @endforeach
                @endforelse
            </div>

            {{-- Chọn ngày --}}
            <div class="mt-6">
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-4">
                    <i class="fa-regular fa-calendar text-red-500 mr-2"></i>Ngày Chiếu
                </h3>
                <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                    @for ($i = 0; $i < 5; $i++)
                    <div class="flex-shrink-0 w-16 h-20 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors
                        {{ $i === 0 ? 'bg-red-600 text-white' : 'bg-gray-800 border border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        <span class="text-xs uppercase">{{ date('D', strtotime("+{$i} days")) }}</span>
                        <span class="font-bold text-xl">{{ date('d', strtotime("+{$i} days")) }}</span>
                        <span class="text-xs">{{ date('M', strtotime("+{$i} days")) }}</span>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Link xem tất cả rạp --}}
            <a
                href="/cinemas"
                class="block w-full text-center py-2.5 border border-red-500/50 text-red-400 hover:bg-red-600/10 rounded-lg text-sm font-medium transition-colors mt-4"
            >
                <i class="fa-solid fa-map-location-dot mr-2"></i>Xem Tất Cả Rạp
            </a>
        </div>

        {{-- ===== DANH SÁCH PHIM ===== --}}
        <div class="md:col-span-3 space-y-6" id="movieList">

            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">
                    {{ date('d/m/Y') }} — Tất Cả Rạp
                </h2>
                <span class="text-sm text-gray-500" id="movieCount">
                    {{ count($movies ?? []) }} phim
                </span>
            </div>

            @forelse($movies ?? [] as $movie)
            <div
                class="movie-item flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-red-500/40 hover:shadow-lg hover:shadow-red-500/5 transition-all"
                data-name="{{ strtolower($movie->name) }}"
                data-genre="{{ strtolower($movie->genre ?? '') }}"
            >
                {{-- Poster --}}
                <div class="w-full md:w-44 h-64 md:h-auto flex-shrink-0 overflow-hidden">
                    <img
                        src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=300&h=400&auto=format&fit=crop' }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        alt="{{ $movie->name }}"
                    >
                </div>

                {{-- Thông tin phim --}}
                <div class="p-6 flex-1 flex flex-col">
                    {{-- Badges --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex gap-2">
                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">
                                {{ $movie->age_limit ?? 'T16' }}
                            </span>
                            <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">
                                2D Phụ Đề
                            </span>
                        </div>
                        @if(isset($movie->rating))
                        <div class="text-yellow-500 text-sm font-bold">
                            <i class="fa-solid fa-star mr-1"></i>{{ number_format($movie->rating, 1) }}
                        </div>
                        @endif
                    </div>

                    {{-- Tên phim --}}
                    <h3 class="text-2xl font-bold text-white mb-2">{{ $movie->name }}</h3>

                    {{-- Mô tả --}}
                    @if($movie->description)
                    <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $movie->description }}</p>
                    @endif

                    {{-- Thông tin --}}
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-3">
                        <span><i class="fa-regular fa-clock mr-1"></i>{{ $movie->duration ?? 120 }} phút</span>
                        <span class="text-gray-700">|</span>
                        <span><i class="fa-solid fa-film mr-1"></i>{{ $movie->genre ?? 'Hành Động' }}</span>
                        @if($movie->release_date ?? null)
                        <span class="text-gray-700">|</span>
                        <span><i class="fa-regular fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</span>
                        @endif
                    </p>

                    {{-- Suất chiếu --}}
                    <div class="mt-auto">
                        <p class="text-sm font-semibold text-gray-300 mb-3">Chọn suất chiếu:</p>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($movie->showtimes) && $movie->showtimes->count() > 0)
                                @foreach($movie->showtimes as $showtime)
                                <a
                                    href="{{ route('booking.show', $showtime->id) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium"
                                >
                                    {{ \Carbon\Carbon::parse($showtime->start_time)->format('H:i') }}
                                </a>
                                @endforeach
                            @else
                                {{-- Fallback suất chiếu mẫu khi chưa có DB --}}
                                @foreach(['09:30', '11:45', '14:20', '17:00', '19:45', '22:15'] as $time)
                                <a
                                    href="{{ route('booking.show', $movie->id) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium"
                                >
                                    {{ $time }}
                                </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <i class="fa-solid fa-film text-5xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg font-semibold">Chưa có phim nào</p>
                <p class="text-gray-600 text-sm mt-2">Chạy <code class="bg-gray-900 px-2 py-0.5 rounded text-red-400">php artisan migrate --seed</code> để thêm dữ liệu mẫu</p>
            </div>
            @endforelse

            {{-- No filter result --}}
            <div id="noMovieResult" class="hidden bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg font-semibold">Không tìm thấy phim phù hợp</p>
            </div>

        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
function filterMovies() {
    const search = document.getElementById('movieSearch').value.toLowerCase().trim();
    const genre  = document.getElementById('genreFilter').value.toLowerCase();
    const items  = document.querySelectorAll('.movie-item');
    let visible  = 0;

    items.forEach(item => {
        const name    = item.dataset.name;
        const itemGenre = item.dataset.genre;
        const matchSearch = !search || name.includes(search);
        const matchGenre  = !genre  || itemGenre.includes(genre);

        if (matchSearch && matchGenre) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('movieCount').textContent = visible + ' phim';
    document.getElementById('noMovieResult').classList.toggle('hidden', visible > 0);
}
</script>

@endsection
