@extends('layouts.app')

@section('content')

@php
// =====================================================
// DỮ LIỆU MẪU — Thay bằng DB sau khi có migration
// =====================================================
$cinemaList = [
    ['id' => 'lm81', 'name' => 'CineBook Landmark 81'],
    ['id' => 'svh',  'name' => 'CineBook Sư Vạn Hạnh'],
    ['id' => 'gv',   'name' => 'CineBook Gò Vấp'],
    ['id' => 'q7',   'name' => 'CineBook Quận 7'],
    ['id' => 'td',   'name' => 'CineBook Thủ Đức'],
];

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

// Phim mẫu với cinema_ids và showtimes theo ngày
$mockMovies = [
    [
        'id'          => 1,
        'name'        => 'Avengers: Secret Wars',
        'genre'       => 'Hành Động',
        'duration'    => 150,
        'age_limit'   => 'T16',
        'rating'      => 9.2,
        'description' => 'Cuộc chiến cuối cùng định đoạt số phận đa vũ trụ. Tất cả các siêu anh hùng tập hợp để đối mặt với mối đe dọa lớn nhất trong lịch sử.',
        'poster'      => 'https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['lm81', 'svh', 'q7'],
        'showtimes'   => [
            date('Y-m-d')                         => ['09:00', '11:30', '14:00', '17:00', '20:00', '22:30'],
            date('Y-m-d', strtotime('+1 days'))   => ['10:00', '13:00', '16:30', '19:30', '22:00'],
            date('Y-m-d', strtotime('+2 days'))   => ['09:30', '12:30', '15:30', '18:30', '21:30'],
            date('Y-m-d', strtotime('+3 days'))   => ['10:30', '14:00', '17:30', '21:00'],
            date('Y-m-d', strtotime('+4 days'))   => ['11:00', '14:30', '18:00', '21:30'],
        ],
    ],
    [
        'id'          => 2,
        'name'        => 'Lật Mặt 8: Vòng Tay Nắng',
        'genre'       => 'Hài Hước',
        'duration'    => 128,
        'age_limit'   => 'T13',
        'rating'      => 8.7,
        'description' => 'Hành trình cảm động về tình thân và những nút thắt gia đình được tháo gỡ qua những tình huống vừa hài vừa xúc động.',
        'poster'      => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['lm81', 'gv', 'td'],
        'showtimes'   => [
            date('Y-m-d')                         => ['08:30', '11:00', '13:30', '16:00', '19:00', '21:30'],
            date('Y-m-d', strtotime('+1 days'))   => ['09:00', '11:30', '14:30', '17:30', '20:30'],
            date('Y-m-d', strtotime('+2 days'))   => ['10:00', '12:30', '15:00', '18:00', '20:30'],
            date('Y-m-d', strtotime('+3 days'))   => ['09:30', '12:00', '15:30', '19:00', '21:30'],
            date('Y-m-d', strtotime('+4 days'))   => ['10:30', '13:30', '16:30', '20:00'],
        ],
    ],
    [
        'id'          => 3,
        'name'        => 'Minecraft: The Movie',
        'genre'       => 'Hoạt Hình',
        'duration'    => 110,
        'age_limit'   => 'P',
        'rating'      => 8.1,
        'description' => 'Cuộc phiêu lưu kỳ thú trong thế giới Minecraft khi nhóm bạn trẻ bị cuốn vào vùng đất khối vuông và phải tìm đường trở về.',
        'poster'      => 'https://images.unsplash.com/photo-1560169897-fc0cdbdfa4d5?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['svh', 'gv', 'q7', 'td'],
        'showtimes'   => [
            date('Y-m-d')                         => ['09:15', '11:45', '14:15', '16:45', '19:15'],
            date('Y-m-d', strtotime('+1 days'))   => ['10:15', '12:45', '15:15', '17:45', '20:15'],
            date('Y-m-d', strtotime('+2 days'))   => ['09:45', '12:15', '14:45', '17:15', '19:45'],
            date('Y-m-d', strtotime('+3 days'))   => ['10:45', '13:15', '15:45', '18:15'],
            date('Y-m-d', strtotime('+4 days'))   => ['11:15', '13:45', '16:15', '18:45'],
        ],
    ],
    [
        'id'          => 4,
        'name'        => 'Linh Miêu: Quỷ Nhập Tràng',
        'genre'       => 'Kinh Dị',
        'duration'    => 105,
        'age_limit'   => 'T18',
        'rating'      => 7.8,
        'description' => 'Câu chuyện kinh dị về những thực thể bóng tối ẩn nấp trong ngôi làng cổ, nơi ranh giới giữa thế giới người và âm phủ mong manh.',
        'poster'      => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['lm81', 'q7'],
        'showtimes'   => [
            date('Y-m-d')                         => ['10:00', '12:30', '15:30', '18:30', '21:00', '23:00'],
            date('Y-m-d', strtotime('+1 days'))   => ['11:00', '14:00', '17:00', '20:00', '22:30'],
            date('Y-m-d', strtotime('+2 days'))   => ['10:30', '13:30', '16:30', '19:30', '22:00'],
            date('Y-m-d', strtotime('+3 days'))   => ['11:30', '14:30', '17:30', '21:00'],
            date('Y-m-d', strtotime('+4 days'))   => ['12:00', '15:00', '18:00', '21:00'],
        ],
    ],
    [
        'id'          => 5,
        'name'        => 'Em Và Trịnh',
        'genre'       => 'Tình Cảm',
        'duration'    => 135,
        'age_limit'   => 'T13',
        'rating'      => 8.4,
        'description' => 'Câu chuyện tình yêu đẹp nhưng đau thương của nhạc sĩ Trịnh Công Sơn và những người phụ nữ đi qua cuộc đời ông.',
        'poster'      => 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['svh', 'gv', 'td'],
        'showtimes'   => [
            date('Y-m-d')                         => ['08:00', '10:30', '13:00', '15:30', '18:00', '20:30'],
            date('Y-m-d', strtotime('+1 days'))   => ['09:00', '11:30', '14:00', '16:30', '19:30'],
            date('Y-m-d', strtotime('+2 days'))   => ['08:30', '11:00', '13:30', '16:00', '18:30', '21:00'],
            date('Y-m-d', strtotime('+3 days'))   => ['09:30', '12:00', '14:30', '17:30', '20:00'],
            date('Y-m-d', strtotime('+4 days'))   => ['10:00', '12:30', '15:00', '18:00', '20:30'],
        ],
    ],
    [
        'id'          => 6,
        'name'        => 'Fast & Furious 11',
        'genre'       => 'Hành Động',
        'duration'    => 145,
        'age_limit'   => 'T16',
        'rating'      => 8.9,
        'description' => 'Hành trình cuối cùng của gia đình Toretto — những pha hành động nghẹt thở và cảm xúc dâng trào trong chương kết huyền thoại.',
        'poster'      => 'https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?q=80&w=300&h=400&auto=format&fit=crop',
        'cinema_ids'  => ['lm81', 'svh', 'gv', 'q7', 'td'],
        'showtimes'   => [
            date('Y-m-d')                         => ['08:45', '11:15', '13:45', '16:15', '18:45', '21:15', '23:30'],
            date('Y-m-d', strtotime('+1 days'))   => ['09:45', '12:15', '14:45', '17:15', '19:45', '22:15'],
            date('Y-m-d', strtotime('+2 days'))   => ['10:15', '12:45', '15:15', '17:45', '20:15', '22:45'],
            date('Y-m-d', strtotime('+3 days'))   => ['09:15', '11:45', '14:15', '17:45', '20:45'],
            date('Y-m-d', strtotime('+4 days'))   => ['10:45', '13:15', '15:45', '18:15', '21:00'],
        ],
    ],
];

// Dùng DB nếu có, fallback sang mock
$movies     = isset($movies) && count($movies) > 0 ? $movies : collect($mockMovies);
$cinemas    = isset($cinemas) && count($cinemas) > 0 ? $cinemas : collect($cinemaList)->map(fn($c) => (object)$c);
$todayStr   = date('Y-m-d');
@endphp

{{-- ===== HEADER ===== --}}
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- ===== SIDEBAR ===== --}}
        <div class="md:col-span-1 space-y-4">

            {{-- Chọn Rạp --}}
            <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2">
                <i class="fa-solid fa-building text-red-500 mr-2"></i>Chọn Rạp
            </h3>
            <div class="space-y-2">
                <button onclick="selectCinema(this, 'all')" data-cinema="all"
                    class="cinema-btn active w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors">
                    <span class="font-medium text-sm"><i class="fa-solid fa-globe mr-2"></i>Tất Cả Rạp</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
                @foreach($cinemas as $cinema)
                <button onclick="selectCinema(this, '{{ $cinema->id }}')" data-cinema="{{ $cinema->id }}"
                    class="cinema-btn w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 hover:text-white">
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
                <h2 class="text-xl font-bold text-white" id="pageTitle">
                    {{ date('d/m/Y') }} — Tất Cả Rạp
                </h2>
                <span class="text-sm text-gray-500" id="movieCount">{{ count($mockMovies) }} phim</span>
            </div>

            <div id="movieList" class="space-y-6">
                @foreach($mockMovies as $movie)
                <div
                    class="movie-item flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-red-500/40 hover:shadow-lg hover:shadow-red-500/5 transition-all"
                    data-id="{{ $movie['id'] }}"
                    data-name="{{ strtolower($movie['name']) }}"
                    data-genre="{{ strtolower($movie['genre']) }}"
                    data-cinemas="{{ implode(',', $movie['cinema_ids']) }}"
                    data-showtimes='@json($movie['showtimes'])'
                >
                    {{-- Poster --}}
                    <div class="w-full md:w-44 h-64 md:h-auto flex-shrink-0 overflow-hidden">
                        <img src="{{ $movie['poster'] }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            alt="{{ $movie['name'] }}">
                    </div>

                    {{-- Info --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex gap-2">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded">{{ $movie['age_limit'] }}</span>
                                <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">2D Phụ Đề</span>
                                <span class="text-xs border border-gray-600 text-gray-400 px-2 py-0.5 rounded">{{ $movie['genre'] }}</span>
                            </div>
                            <div class="text-yellow-500 text-sm font-bold">
                                <i class="fa-solid fa-star mr-1"></i>{{ $movie['rating'] }}
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-white mb-2">{{ $movie['name'] }}</h3>
                        <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $movie['description'] }}</p>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $movie['duration'] }} phút
                        </p>

                        {{-- Suất chiếu — cập nhật bằng JS --}}
                        <div class="mt-auto">
                            <p class="text-sm font-semibold text-gray-300 mb-3">
                                <i class="fa-solid fa-ticket text-red-500 mr-1.5"></i>Chọn suất chiếu:
                            </p>
                            <div class="showtime-list flex flex-wrap gap-2">
                                {{-- Render suất chiếu hôm nay mặc định --}}
                                @foreach($movie['showtimes'][date('Y-m-d')] ?? [] as $time)
                                <a href="{{ route('booking.show', $movie['id']) }}"
                                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                                    {{ $time }}
                                </a>
                                @endforeach
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
    'lm81': 'CineBook Landmark 81',
    'svh':  'CineBook Sư Vạn Hạnh',
    'gv':   'CineBook Gò Vấp',
    'q7':   'CineBook Quận 7',
    'td':   'CineBook Thủ Đức',
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
        const itemCinemas  = item.dataset.cinemas; // vd: "lm81,svh,q7"
        const allShowtimes = JSON.parse(item.dataset.showtimes); // object {date: [times]}

        // Kiểm tra từng điều kiện
        const matchSearch  = !search || name.includes(search);
        const matchGenre   = !genre  || itemGenre.includes(genre);
        const matchCinema  = activeCinema === 'all' || itemCinemas.split(',').includes(activeCinema);
        const timesForDate = allShowtimes[activeDate] ?? [];
        const matchDate    = timesForDate.length > 0;

        if (matchSearch && matchGenre && matchCinema && matchDate) {
            item.style.display = '';
            visible++;

            // Cập nhật suất chiếu theo ngày đang chọn
            const showtimeContainer = item.querySelector('.showtime-list');
            const movieId = item.dataset.id;
            showtimeContainer.innerHTML = timesForDate.map(time =>
                `<a href="/booking/${movieId}"
                    class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-400 transition-colors text-sm font-medium">
                    ${time}
                </a>`
            ).join('');
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