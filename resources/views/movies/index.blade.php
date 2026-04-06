@extends('layouts.app')

@section('content')
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Đang Chiếu Tại Rạp</h1>
        
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 mb-8 bg-gray-800 p-4 rounded-xl border border-gray-700">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-500"></i>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-1 focus:ring-red-500 focus:border-red-500" placeholder="Tìm tên phim, diễn viên, đạo diễn...">
                </div>
            </div>
            
            <select class="bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                <option>Toàn Quốc</option>
                <option>Hồ Chí Minh</option>
                <option>Hà Nội</option>
                <option>Đà Nẵng</option>
            </select>
            
            <select class="bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none">
                <option>Tất cả thể loại</option>
                <option>Hành Động</option>
                <option>Hài Hước</option>
                <option>Kinh Dị</option>
            </select>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- Rạp Chiếu Phim Sidebar -->
        <div class="md:col-span-1 space-y-4">
            <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2">Danh Sách Rạp Bán Vé</h3>
            <div class="space-y-2">
                @php
                    $theaters = ['CineBook Landmark 81', 'CineBook Sư Vạn Hạnh', 'CineBook Gò Vấp', 'CineBook Quận 7', 'CineBook Thủ Đức'];
                @endphp
                @foreach($theaters as $index => $theater)
                <button class="w-full text-left px-4 py-3 rounded-lg flex items-center justify-between transition-colors {{ $index === 0 ? 'bg-red-600/20 border border-red-500 text-red-400' : 'bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700' }}">
                    <span class="font-medium text-sm">{{ $theater }}</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
                @endforeach
            </div>
            
            <div class="mt-8">
                <h3 class="font-bold text-white text-lg border-b border-gray-700 pb-2 mb-4">Ngày Chiếu</h3>
                <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                    @for ($i = 0; $i < 5; $i++)
                    <div class="flex-shrink-0 w-16 h-20 rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors {{ $i === 0 ? 'bg-red-600 text-white' : 'bg-gray-800 border border-gray-700 text-gray-400 hover:bg-gray-700' }}">
                        <span class="text-xs uppercase">{{ date('D', strtotime("+$i days")) }}</span>
                        <span class="font-bold text-xl">{{ date('d', strtotime("+$i days")) }}</span>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Danh Sách Phim Của Rạp -->
        <div class="md:col-span-3 space-y-6">
            <h2 class="text-xl font-bold text-white">CineBook Landmark 81 - {{ date('d/m/Y') }}</h2>
            
            @forelse($movies as $movie)
            <div class="flex flex-col md:flex-row bg-gray-800 rounded-xl border border-gray-700 overflow-hidden hover:border-gray-600 transition-colors">
                <img src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=300&h=400&auto=format&fit=crop' }}" class="w-full md:w-48 h-64 md:h-auto object-cover" alt="Poster {{ $movie->name }}">
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded mr-2">{{ $movie->age_limit ?? 'T16' }}</span>
                            <span class="text-xs border border-blue-500 text-blue-400 px-2 py-0.5 rounded">2D Phụ Đề</span>
                        </div>
                        <div class="text-yellow-500 text-sm font-bold"><i class="fa-solid fa-star mr-1"></i>{{ $movie->rating ?? 8.5 }}</div>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-white mb-2">{{ $movie->name }}</h3>
                    @if($movie->description)
                    <p class="text-sm text-gray-300 mb-2">{{ $movie->description }}</p>
                    @endif
                    <p class="text-sm text-gray-400 mb-4 flex items-center">
                        <i class="fa-regular fa-clock mr-2"></i> {{ is_numeric($movie->duration) ? $movie->duration . ' Phút' : ($movie->duration ?? '120 Phút') }} | {{ $movie->genre ?? 'Hành Động' }}
                    </p>
                    
                    <div class="mt-auto">
                        <p class="text-sm font-semibold text-gray-300 mb-3">Chọn suất chiếu:</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($movie->show_times ?? ['09:30', '11:45', '14:20', '17:00', '19:45', '22:15'] as $time)
                            <a href="/booking/{{ $movie->id }}" class="px-4 py-2 bg-gray-900 border border-gray-600 text-gray-200 rounded-md hover:border-red-500 hover:text-red-500 transition-colors text-sm font-medium">
                                {{ $time }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-8 text-gray-300">
                Hiện chưa có phim để hiển thị. Hãy chạy lại `php artisan migrate --seed` để thêm dữ liệu mẫu.
            </div>
            @endforelse
            
        </div>
    </div>
</div>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
@endsection
