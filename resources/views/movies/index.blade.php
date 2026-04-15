@extends('layouts.app')

@section('content')
<<<<<<< HEAD
@php
    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate ?? now()->toDateString());
    $selectedCinemaName = $selectedCinema->name ?? 'Tất Cả Rạp';
@endphp
=======

@php
// Tạo mảng 5 ngày
$dates = [];
for ($i = 0; $i < 5; $i++) {
    $dates[] = [
        'value'   => date('Y-m-d', strtotime("+{$i} days")),
        'label'   => $i === 0 ? 'Hôm nay' : date('D', strtotime("+{$i} days")),
        'day'     => date('d', strtotime("+{$i} days")),
        'month'   => date('M', strtotime("+{$i} days")),
    ];
}

$todayStr = date('Y-m-d');
@endphp

{{-- ===== HEADER ===== --}}
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Đang Chiếu Tại Rạp</h1>
        <div class="flex flex-col md:flex-row gap-4 bg-gray-800 p-4 rounded-xl border border-gray-700">
            <div class="flex-1 relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="text" id="movieSearch" placeholder="Tìm tên phim, thể loại..."
                    onkeyup="applyFilters()"
                    class="block w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
            </div>
            <select id="genreFilter" onchange="applyFilters()"
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
<<<<<<< HEAD

            <div class="space-y-2" id="cinemaList">
                @forelse($cinemas ?? [] as $index => $cinema)
                <a
                    href="{{ route('movies.index', ['cinema' => $cinema->id, 'date' => $selectedDateCarbon->toDateString()]) }}"
                    class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors block
                        {{ (int) ($selectedCinemaId ?? 0) === (int) $cinema->id ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white' }}"
                >
=======
            <div class="space-y-2">
                <button onclick="selectCinema(this, 'all')" data-cinema="all"
                    class="cinema-btn active w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors">
                    <span class="font-medium text-sm"><i class="fa-solid fa-globe mr-2"></i>Tất Cả Rạp</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
                @foreach($cinemas as $cinema)
                <button onclick="selectCinema(this, '{{ $cinema->id }}')" data-cinema="{{ $cinema->id }}"
                    class="cinema-btn w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white">
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
                    <span class="font-medium text-sm">{{ $cinema->name }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
                @endforeach
            </div>

            {{-- Chọn Ngày --}}
            <div class="mt-6">
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-4">
                    <i class="fa-regular fa-calendar text-red-500 mr-2"></i>Ngày Chiếu
                </h3>
                <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
<<<<<<< HEAD
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
=======
                    @foreach($dates as $i => $date)
                    <div
                        onclick="selectDate(this, '{{ $date['value'] }}')"
                        data-date="{{ $date['value'] }}"
                        class="date-btn flex-shrink-0 w-16 h-20 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors
                            {{ $i === 0 ? 'bg-red-600 text-white' : 'bg-gray-800 border border-gray-700 text-gray-400 hover:bg-gray-700' }}"
                    >
                        <span class="text-xs uppercase font-semibold">{{ $date['label'] }}</span>
                        <span class="font-bold text-xl">{{ $date['day'] }}</span>
                        <span class="text-xs opacity-80">{{ $date['month'] }}</span>
                    </div>
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
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
<<<<<<< HEAD
                <h2 class="text-xl font-bold text-white">
                    {{ $selectedDateCarbon->format('d/m/Y') }} — {{ $selectedCinemaName }}
=======
                <h2 class="text-xl font-bold text-white" id="pageTitle">
                    {{ date('d/m/Y') }} — Tất Cả Rạp
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
                </h2>
                <span class="text-sm text-gray-500" id="movieCount">{{ count($movies ?? []) }} phim</span>
            </div>

<<<<<<< HEAD
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
                                {{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}
                            </span>
                            @if(isset($movie->showtimes) && $movie->showtimes->count() > 0)
                                <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">
                                    {{ $movie->showtimes->first()->subtitle_name ?: '2D Phụ Đề' }}
                                </span>
                            @endif
                        </div>
                        @if(isset($movie->rating))
                        <div class="text-yellow-500 text-sm font-bold">
                            <i class="fa-solid fa-star mr-1"></i>{{ number_format($movie->rating, 1) }}
                        </div>
                        @endif
=======
            <div id="movieList" class="space-y-6">
                @foreach($movies as $movie)
                <div
                    class="movie-item flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-red-500/40 hover:shadow-lg hover:shadow-red-500/5 transition-all"
                    data-id="{{ $movie->id }}"
                    data-name="{{ strtolower($movie->name) }}"
                    data-genre="{{ strtolower($movie->genre) }}"
                    data-showing-dates="{{ json_encode($movieShowingDates[$movie->name] ?? []) }}"
                >
                    {{-- Poster --}}
                    <div class="w-full md:w-44 h-64 md:h-auto flex-shrink-0 overflow-hidden">
                        <img src="{{ $movie->poster ?? 'https://via.placeholder.com/300x400?text=No+Image' }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            alt="{{ $movie->name }}">
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
                    </div>

                    {{-- Info --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex gap-2">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">T{{ $movie->age_limit }}</span>
                                <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">2D Phụ Đề</span>
                                <span class="text-xs border border-gray-600 text-gray-400 px-2 py-0.5 rounded">{{ $movie->genre }}</span>
                            </div>
                            <div class="text-yellow-500 text-sm font-bold">
                                <i class="fa-solid fa-star mr-1"></i>{{ rand(73, 95) / 10 }}
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-white mb-2">{{ $movie->name }}</h3>
                        <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $movie->description ?? 'Không có mô tả' }}</p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $movie->duration ?? 120 }} phút
                        </p>

<<<<<<< HEAD
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
                                    title="{{ trim(($showtime->cinema_name ?? '') . ' - ' . ($showtime->room_name ?? '')) }}"
                                >
                                    {{ \Carbon\Carbon::parse($showtime->start_time)->format('H:i') }}
=======
                        {{-- Suất chiếu -- cập nhật bằng JS --}}
                        <div class="mt-auto">
                            <p class="text-sm font-semibold text-gray-300 mb-3">
                                <i class="fa-solid fa-ticket text-red-500 mr-1.5"></i>Chọn suất chiếu:
                            </p>
                            <div class="showtime-list flex flex-wrap gap-2">
                                <a href="{{ route('booking.show', $movie->id) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                                    09:00
>>>>>>> caadfaab0b0675e8546d2e43125a08a41c10e783
                                </a>
                                <a href="{{ route('booking.show', $movie->id) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                                    14:00
                                </a>
                                <a href="{{ route('booking.show', $movie->id) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                                    19:00
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- No result --}}
            <div id="noMovieResult" class="hidden bg-gray-800 rounded-xl border border-gray-700 p-12 text-center">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg font-semibold">Không có phim nào phù hợp</p>
                <p class="text-gray-600 text-sm mt-1">Thử chọn rạp khác hoặc ngày khác</p>
            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

.cinema-btn.active {
    background-color: rgba(220,38,38,0.2) !important;
    border: 1px solid rgb(239,68,68) !important;
    color: rgb(248,113,113) !important;
}
.date-btn.active {
    background-color: #dc2626 !important;
    color: white !important;
    border: none !important;
}
</style>

<script>
// Trạng thái filter đang chọn
let activeCinema = 'all';
let activeDate   = '{{ date("Y-m-d") }}';

// Map tên rạp để hiển thị tiêu đề
const cinemaNames = {
    'all':  'Tất Cả Rạp',
    @foreach($cinemas as $c)
    '{{ $c->id }}': '{{ $c->name }}',
    @endforeach
};

// Map ngày để hiển thị tiêu đề
const dateLabels = {
    @foreach($dates as $d)
    '{{ $d['value'] }}': '{{ $d['day'] }}/{{ date("m", strtotime($d['value'])) }}/{{ date("Y", strtotime($d['value'])) }}',
    @endforeach
};

// ========================
// Chọn RẠP
// ========================
function selectCinema(btn, cinemaId) {
    document.querySelectorAll('.cinema-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeCinema = cinemaId;
    applyFilters();
}

// ========================
// Chọn NGÀY
// ========================
function selectDate(btn, dateVal) {
    document.querySelectorAll('.date-btn').forEach(b => {
        b.classList.remove('active', 'bg-red-600', 'text-white');
        b.classList.add('bg-gray-800', 'border', 'border-gray-700', 'text-gray-400');
    });
    btn.classList.add('active', 'bg-red-600', 'text-white');
    btn.classList.remove('bg-gray-800', 'border-gray-700', 'text-gray-400');
    activeDate = dateVal;
    applyFilters();
}

// ========================
// ÁP DỤNG TẤT CẢ FILTER
// ========================
function applyFilters() {
    const search = document.getElementById('movieSearch').value.toLowerCase().trim();
    const genre  = document.getElementById('genreFilter').value.toLowerCase();
    const items  = document.querySelectorAll('.movie-item');
    let visible  = 0;

    items.forEach(item => {
        const name         = item.dataset.name;
        const itemGenre    = item.dataset.genre;
        const showingDates = JSON.parse(item.dataset.showingDates || '[]');

        // Kiểm tra từng điều kiện
        const matchSearch = !search || name.includes(search);
        const matchGenre  = !genre || itemGenre.includes(genre);
        const matchDate   = showingDates.includes(activeDate);

        if (matchSearch && matchGenre && matchDate) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    // Cập nhật tiêu đề
    const cinemaLabel = cinemaNames[activeCinema] ?? 'Tất Cả Rạp';
    const dateLabel   = dateLabels[activeDate] ?? activeDate;
    document.getElementById('pageTitle').textContent = dateLabel + ' — ' + cinemaLabel;
    document.getElementById('movieCount').textContent = visible + ' phim';
    document.getElementById('noMovieResult').classList.toggle('hidden', visible > 0);
}
</script>

@endsection