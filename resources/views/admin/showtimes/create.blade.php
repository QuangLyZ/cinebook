@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="max-w-2xl space-y-6 animate-[fadeIn_0.5s_ease-in-out]">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.showtimes.index') }}"
           class="text-[rgb(255,255,255)] transition hover:text-gray-300">
            <i class="fa-solid fa-chevron-left text-2xl"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">Thêm Suất Chiếu Mới</h2>
            <p class="mt-1 text-sm text-gray-400">Chọn phim, phòng chiếu và thời gian để tạo suất chiếu.</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="rounded-2xl border border-red-500/30 bg-red-500/10 p-4">
            <ul class="space-y-1 text-sm text-red-400">
                @foreach($errors->all() as $e)
                    <li><i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.showtimes.store') }}" class="space-y-5">
        @csrf

        <div class="rounded-3xl border border-gray-800 bg-gray-900/80 p-6 space-y-5">

            {{-- Movie --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Phim <span class="text-red-400">*</span></label>
                <select name="movie_id" id="movie_select"
                        class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 appearance-none">
                    <option value="">— Chọn phim —</option>
                    @foreach($movies as $movie)
                        <option value="{{ $movie->id }}"
                                data-duration="{{ $movie->duration }}"
                                {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                            {{ $movie->name }} {{ $movie->duration ? '(' . $movie->duration . ' phút)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Cinema → Room --}}
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Cụm Rạp <span class="text-red-400">*</span></label>
                    <select id="cinema_select"
                            class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 appearance-none">
                        <option value="">— Chọn rạp —</option>
                        @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-300">Phòng Chiếu <span class="text-red-400">*</span></label>
                    <select name="room_id" id="room_select"
                            class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 appearance-none">
                        <option value="">— Chọn rạp trước —</option>
                    </select>
                </div>
            </div>

            {{-- Subtitle --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Ngôn ngữ / Phụ đề <span class="text-red-400">*</span></label>
                <select name="subtitle_id"
                        class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 appearance-none">
                    <option value="">— Chọn ngôn ngữ —</option>
                    @foreach($subtitles as $sub)
                        <option value="{{ $sub->id }}" {{ old('subtitle_id') == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
                @if($subtitles->isEmpty())
                    <p class="mt-2 text-xs text-amber-400">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Chưa có ngôn ngữ nào. Vui lòng thêm vào bảng <code>subtitles</code> trong database.
                    </p>
                @endif
            </div>

            {{-- Start Time --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-300">Thời gian bắt đầu <span class="text-red-400">*</span></label>
                <input type="datetime-local" name="start_time" value="{{ old('start_time') }}"
                       class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fa-solid fa-shield-halved mr-1 text-violet-400"></i>
                    Hệ thống sẽ tự động kiểm tra trùng lịch với các suất chiếu trong cùng phòng.
                </p>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.showtimes.index') }}"
               class="rounded-xl border border-gray-700 bg-transparent px-5 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-gray-800">
                Hủy
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-violet-900/30 transition hover:bg-violet-700">
                <i class="fa-solid fa-calendar-plus"></i> Tạo Suất Chiếu
            </button>
        </div>
    </form>
</div>

@php
    $cinemasJson = $cinemas->map(fn($c) => [
        'id' => $c->id,
        'rooms' => $c->rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name, 'seat_count' => $r->seat_count])
    ]);
@endphp

<script>
    const cinemasData = @json($cinemasJson);

    document.getElementById('cinema_select').addEventListener('change', function () {
        const cinemaId = parseInt(this.value);
        const roomSelect = document.getElementById('room_select');
        roomSelect.innerHTML = '<option value="">— Chọn phòng —</option>';

        const cinema = cinemasData.find(c => c.id === cinemaId);
        if (cinema) {
            cinema.rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name + (r.seat_count ? ` (${r.seat_count} ghế)` : '');
                roomSelect.appendChild(opt);
            });
        } else {
            roomSelect.innerHTML = '<option value="">— Chọn rạp trước —</option>';
        }
    });
</script>
@endsection
