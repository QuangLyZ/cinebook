@extends('layouts.app')
@section('content')
<div class="relative bg-gray-950 overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(239,68,68,0.15),_transparent_60%)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_rgba(239,68,68,0.08),_transparent_60%)]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
            Danh Sách Rạp <span class="text-red-500">CineBook</span>
        </h1>
    </div>
</div>

{{-- Filter & Search Bar --}}
<div class="bg-gray-900 border-b border-gray-800 sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Tìm tên rạp, địa chỉ, quận..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors placeholder-gray-500"
                    onkeyup="filterTheaters()"
                >
            </div>
            <select
                id="districtFilter"
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors outline-none"
                onchange="filterTheaters()"
            >
                <option value="">Tất cả Quận / Huyện</option>
                {{-- Tự động lấy danh sách quận từ DB, không hardcode --}}
                @foreach($cinemas->pluck('district')->unique()->sort()->values() as $district)
                    <option value="{{ $district }}">{{ $district }}</option>
                @endforeach
            </select>
            <select
                id="featureFilter"
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors outline-none"
                onchange="filterTheaters()"
            >
                <option value="">Tất cả Tiện Ích</option>
                <option value="IMAX">IMAX</option>
                <option value="4DX">4DX</option>
                <option value="Dolby">Dolby Atmos</option>
                <option value="VIP">Phòng VIP</option>
            </select>
        </div>
    </div>
</div>

{{-- Theater Grid --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <p class="text-gray-500 text-sm mb-6" id="resultCount">
        Hiện tại đang có <span class="text-white font-semibold">{{ $cinemas->count() }}</span> rạp đang hoạt động
    </p>

    @php
    $badgeColors = [
        'IMAX'  => 'bg-blue-500/20 text-blue-400 border-blue-500/40',
        '4DX'   => 'bg-purple-500/20 text-purple-400 border-purple-500/40',
        'Dolby' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/40',
        'VIP'   => 'bg-red-500/20 text-red-400 border-red-500/40',
    ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="theaterGrid">

        @foreach($cinemas as $cinema)
        <div
            class="theater-card group bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden hover:border-red-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-red-500/10 hover:-translate-y-1 flex flex-col"
            data-district="{{ $cinema->district }}"
            data-features="{{ implode(',', (array) ($cinema->features ?? [])) }}"
            data-name="{{ $cinema->name }}"
            data-address="{{ $cinema->address }}"
        >
            {{-- Thumbnail --}}
            <div class="relative overflow-hidden h-44">
                <img
                    src="{{ $cinema->image ?? asset('images/unnamed.jpg') }}"
                    alt="{{ $cinema->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-gray-800 via-transparent to-transparent"></div>

                {{-- Status badge --}}
                <div class="absolute top-3 left-3">
                    @if($cinema->status === 'open')
                    <span class="flex items-center gap-1.5 bg-black/60 backdrop-blur-sm text-emerald-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Đang Hoạt Động
                    </span>
                    @else
                    <span class="bg-black/60 backdrop-blur-sm text-red-400 text-xs font-semibold px-2.5 py-1 rounded-full border border-red-500/30">
                        Tạm Đóng
                    </span>
                    @endif
                </div>

                {{-- District --}}
                <div class="absolute top-3 right-3">
                    <span class="bg-black/60 backdrop-blur-sm text-gray-300 text-xs px-2.5 py-1 rounded-full border border-gray-600/50">
                        {{ $cinema->district }}
                    </span>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-5 flex flex-col flex-1">
                <h3 class="text-lg font-bold text-white mb-1 group-hover:text-red-400 transition-colors leading-snug">
                    {{ $cinema->name }}
                </h3>

                <p class="text-gray-400 text-sm mb-3 flex items-start gap-2">
                    <i class="fa-solid fa-location-dot text-red-500 mt-0.5 shrink-0"></i>
                    {{ $cinema->address }}, {{ $cinema->district }}, TP.HCM
                </p>

                {{-- Feature Badges --}}
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @php
                        $features = $cinema->features ?? [];
                        if (is_string($features)) {
                            $decoded = json_decode($features, true);
                            $features = is_array($decoded) ? $decoded : ($features === '' ? [] : [$features]);
                        } else {
                            $features = (array) $features;
                        }
                    @endphp

                    @foreach($features as $feature)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded border {{ $badgeColors[$feature] ?? 'bg-gray-700 text-gray-300 border-gray-600' }}">
                        {{ $feature }}
                    </span>
                    @endforeach
                </div>

                {{-- Stats row --}}
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 border-t border-gray-700 pt-4">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-film text-gray-600"></i>
                        {{ $cinema->screens }} phòng chiếu
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-chair text-gray-600"></i>
                        {{ number_format($cinema->seats) }} chỗ
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-gray-600"></i>
                        {{ $cinema->hours }}
                    </span>
                </div>

                {{-- Info row --}}
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-5">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-gray-600"></i>
                        {{ $cinema->phone }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 mt-auto">
                    <a
                        href="{{ route('movies.index', ['theater' => $cinema->name]) }}"
                        class="flex-1 text-center py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-bold rounded-lg transition-colors"
                    >
                        <i class="fa-solid fa-ticket mr-1.5"></i> Mua Vé
                    </a>
                    <a
                        href="{{ route('cinemas.show', $cinema->id) }}"
                        class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition-colors border border-gray-600"
                        title="Chi tiết rạp"
                    >
                        <i>Xem thêm</i>
                    </a>
                    <a
                        href="{{ $cinema->map }}"
                        target="_blank"
                        class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition-colors border border-gray-600"
                        title="Xem bản đồ"
                    >
                        <i class="fa-solid fa-map-location-dot"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- No result --}}
    <div id="noResult" class="hidden text-center py-20">
        <i class="fa-solid fa-magnifying-glass text-5xl text-gray-700 mb-4"></i>
        <p class="text-gray-400 text-lg font-semibold">Không tìm thấy rạp nào phù hợp</p>
        <p class="text-gray-600 text-sm mt-1">Thử thay đổi từ khóa hoặc bộ lọc</p>
    </div>

</div>

{{-- CTA Banner --}}
<div class="bg-gradient-to-r from-red-900/40 via-gray-900 to-gray-900 border-t border-red-900/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-bold text-white mb-2">Chưa tìm được rạp gần bạn?</h2>
            <p class="text-gray-400">Xem lịch chiếu phim theo tất cả rạp tại khu vực của bạn.</p>
        </div>
        <a href="{{ route('movies.index') }}" class="shrink-0 bg-red-600 hover:bg-red-500 text-white font-bold px-8 py-3 rounded-xl transition-colors shadow-lg shadow-red-600/30 flex items-center gap-2">
            <i class="fa-solid fa-film"></i> Xem Lịch Chiếu
        </a>
    </div>
</div>

<script>
function filterTheaters() {
    const search   = document.getElementById('searchInput').value.toLowerCase().trim();
    const district = document.getElementById('districtFilter').value;
    const feature  = document.getElementById('featureFilter').value;
    const cards    = document.querySelectorAll('.theater-card');

    let visible = 0;

    cards.forEach(card => {
        const name     = card.dataset.name.toLowerCase();
        const address  = card.dataset.address.toLowerCase();
        const dist     = card.dataset.district;
    const features = card.dataset.features ? card.dataset.features.split(',').filter(Boolean) : [];

        const matchSearch   = !search   || name.includes(search) || address.includes(search);
        const matchDistrict = !district || dist === district;
        const matchFeature  = !feature  || features.includes(feature);

        if (matchSearch && matchDistrict && matchFeature) {
            card.style.display = '';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('resultCount').innerHTML =
        `Hiện tại đang có <span class="text-white font-semibold">${visible}</span> rạp đang hoạt động`;

    document.getElementById('noResult').classList.toggle('hidden', visible > 0);
}
</script>

@endsection