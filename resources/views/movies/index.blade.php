@extends('layouts.app')

@section('content')
@php
    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate ?? now()->toDateString());
    $selectedCinemaName = $selectedCinema->name ?? 'Tất Cả Rạp';
@endphp

<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Đang Chiếu Tại Rạp</h1>
        <div class="flex flex-col md:flex-row gap-4 bg-gray-800 p-4 rounded-xl border border-gray-700">
            <div class="flex-1 relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="text" id="movieSearch" placeholder="Tìm tên phim, rạp, độ tuổi (T18, T13)..."
                    onkeyup="applyClientFilters()"
                    class="block w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
            </div>
            <select id="genreFilter" onchange="applyClientFilters()"
                class="bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-1 focus:ring-red-500 outline-none">
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
    @if (!empty($dbWarning))
    <div class="mb-6 rounded-xl border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-200">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ $dbWarning }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- ===== SIDEBAR ===== --}}
        <div class="md:col-span-1 space-y-4">

            {{-- Chọn Rạp --}}
            <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2">
                <i class="fa-solid fa-building text-red-500 mr-2"></i>Chọn Rạp
            </h3>

            <div class="space-y-2" id="cinemaList">
                <a
                    href="{{ route('movies.index', ['date' => $selectedDateCarbon->toDateString()]) }}"
                    class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors block
                        {{ is_null($selectedCinemaId) ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' }}"
                >
                    <span class="font-medium text-sm"><i class="fa-solid fa-globe mr-2"></i>Tất Cả Rạp</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>

                @forelse($cinemas ?? [] as $cinema)
                <a
                    href="{{ route('movies.index', ['cinema' => $cinema->id, 'date' => $selectedDateCarbon->toDateString()]) }}"
                    class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors block
                        {{ (int) ($selectedCinemaId ?? 0) === (int) $cinema->id ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' }}"
                >
                    <span class="font-medium text-sm">{{ $cinema->name }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                @empty
                    <p class="text-gray-500 text-sm italic">Đang cập nhật danh sách rạp...</p>
                @endforelse
            </div>

            {{-- Chọn Ngày --}}
            <div class="mt-6">
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-4">
                    <i class="fa-regular fa-calendar text-red-500 mr-2"></i>Ngày Chiếu
                </h3>
                <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                    @foreach (($availableDates ?? collect()) as $date)
                    @php
                        $isActiveDate = $date->toDateString() === $selectedDateCarbon->toDateString();
                    @endphp
                    <a href="{{ route('movies.index', ['cinema' => $selectedCinemaId, 'date' => $date->toDateString()]) }}" class="flex-shrink-0 w-16 h-20 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors
                        {{ $isActiveDate ? 'bg-red-600 text-white' : 'bg-gray-800 border border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        <span class="text-xs uppercase">{{ $date->translatedFormat('D') }}</span>
                        <span class="font-bold text-xl">{{ $date->format('d') }}</span>
                        <span class="text-xs">{{ $date->translatedFormat('M') }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <a href="/cinemas"
                class="block w-full text-center py-2.5 border border-red-500/50 text-red-400 hover:bg-red-600/10 rounded-lg text-sm font-medium transition-colors mt-2">
                <i class="fa-solid fa-map-location-dot mr-2"></i>Xem Tất Cả Rạp
            </a>
        </div>

        {{-- ===== DANH SÁCH PHIM ===== --}}
        <div class="md:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">
                    {{ $selectedDateCarbon->format('d/m/Y') }} — {{ $selectedCinemaName }}
                </h2>
                <span class="text-sm text-gray-500" id="movieCount">{{ count($movies ?? []) }} phim</span>
            </div>

            <div id="movieList" class="space-y-6">
                @forelse($movies ?? [] as $movie)
                @php
                    $cinemaNames = isset($movie->showtimes) ? collect($movie->showtimes)->pluck('cinema_name')->unique()->join(' ') : '';
                    $ageLabel = $movie->age_limit ? 't' . $movie->age_limit : 'p';
                @endphp
                <div
                    class="movie-item flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-red-500/40 hover:shadow-lg hover:shadow-red-500/5 transition-all"
                    data-name="{{ mb_strtolower($movie->name, 'UTF-8') }}"
                    data-genre="{{ mb_strtolower($movie->genre ?? '', 'UTF-8') }}"
                    data-age="{{ mb_strtolower($ageLabel, 'UTF-8') }}"
                    data-cinemas="{{ mb_strtolower($cinemaNames, 'UTF-8') }}"
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
                                    {{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}
                                </span>
                                @if(isset($movie->showtimes) && count($movie->showtimes) > 0)
                                    <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">
                                        {{ $movie->showtimes[0]->subtitle_name ?: '2D Phụ Đề' }}
                                    </span>
                                @endif
                            </div>
                            @if(isset($movie->rating))
                            <div class="text-yellow-500 text-sm font-bold">
                                <i class="fa-solid fa-star mr-1"></i>{{ number_format($movie->rating, 1) }}
                            </div>
                            @endif
                        </div>

                        <h3 class="text-2xl font-bold text-white mb-2">{{ $movie->name }}</h3>
                        <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $movie->description ?? 'Không có mô tả' }}</p>

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
                                @if(isset($movie->showtimes) && count($movie->showtimes) > 0)
                                    @foreach($movie->showtimes as $showtime)
                                    <a
                                        href="{{ route('booking.show', $showtime->id) }}"
                                        class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium"
                                        title="{{ trim(($showtime->cinema_name ?? '') . ' - ' . ($showtime->room_name ?? '')) }}"
                                    >
                                        {{ \Carbon\Carbon::parse($showtime->start_time)->format('H:i') }}
                                    </a>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-xs italic">Không có suất chiếu nào cho ngày này.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div id="noMovieResult" class="bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                    <i class="fa-solid fa-magnifying-glass text-5xl text-gray-600 mb-4"></i>
                    <p class="text-gray-400 text-lg font-semibold">Không có phim nào phù hợp</p>
                    <p class="text-gray-600 text-sm mt-1">Thử chọn rạp khác hoặc ngày khác</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const q = urlParams.get('q');
    if (q) {
        const searchInput = document.getElementById('movieSearch');
        if (searchInput) {
            searchInput.value = q;
            applyClientFilters();
        }
    }
});

function applyClientFilters() {
    const search = document.getElementById('movieSearch').value.toLowerCase().trim();
    const genre  = document.getElementById('genreFilter').value.toLowerCase();
    const items  = document.querySelectorAll('.movie-item');
    let visible  = 0;

    items.forEach(item => {
        const name         = item.dataset.name || '';
        const itemGenre    = item.dataset.genre || '';
        const age          = item.dataset.age || '';
        const cinemas      = item.dataset.cinemas || '';

        const matchSearch = !search || 
                            name.includes(search) || 
                            itemGenre.includes(search) ||
                            age.includes(search) ||
                            cinemas.includes(search);

        const matchGenre  = !genre || itemGenre.includes(genre);

        if (matchSearch && matchGenre) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('movieCount').textContent = visible + ' phim';
}
</script>

@endsection