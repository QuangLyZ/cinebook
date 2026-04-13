@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.management') }}"
               class="text-[rgb(255,255,255)] transition hover:text-gray-300">
                <i class="fa-solid fa-chevron-left text-2xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-white">Quản lý Suất Chiếu</h2>
                <p class="mt-1 text-sm text-gray-400">Lập lịch và xem toàn bộ suất chiếu theo ngày, phòng, rạp.</p>
            </div>
        </div>
        <a href="{{ route('admin.showtimes.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-violet-900/30 transition hover:bg-violet-700">
            <i class="fa-solid fa-plus"></i> Thêm Suất Chiếu
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-400 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.showtimes.index') }}"
          class="flex flex-wrap gap-3 rounded-2xl border border-gray-800 bg-gray-900/60 px-5 py-4">
        <select name="cinema_id"
                class="rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:outline-none appearance-none">
            <option value="">Tất cả rạp</option>
            @foreach($cinemas as $c)
                <option value="{{ $c->id }}" {{ ($filters['cinema_id'] ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        <select name="movie_id"
                class="rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:outline-none appearance-none">
            <option value="">Tất cả phim</option>
            @foreach($movies as $m)
                <option value="{{ $m->id }}" {{ ($filters['movie_id'] ?? '') == $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
               class="rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-violet-500 focus:outline-none">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-violet-700">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>
        @if(array_filter($filters ?? []))
            <a href="{{ route('admin.showtimes.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-gray-800">
                <i class="fa-solid fa-xmark"></i> Xoá lọc
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Phim</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Rạp / Phòng</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Ngôn ngữ</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Thời gian chiếu</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Vé đặt</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($showtimes as $st)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">{{ $st->movie?->name ?? '—' }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $st->movie?->duration ? $st->movie->duration . ' phút' : '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-200">{{ $st->room?->cinema?->name ?? '—' }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $st->room?->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-violet-500/10 px-3 py-1 text-xs font-semibold text-violet-400">
                                    {{ $st->subtitle?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-white">{{ $st->start_time?->format('H:i') }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $st->start_time?->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-bold text-amber-400">
                                    {{ $st->tickets_count ?? $st->tickets()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right border-l border-gray-800/50">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.showtimes.edit', $st) }}"
                                       class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400 transition hover:bg-violet-500/20"
                                       title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.showtimes.destroy', $st) }}" method="POST"
                                          onsubmit="return confirm('Xóa suất chiếu này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/10 text-red-500 transition hover:bg-red-500/20"
                                                title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-calendar-xmark text-4xl mb-3 text-gray-700"></i>
                                    <p class="font-semibold">Không có suất chiếu nào.</p>
                                    <a href="{{ route('admin.showtimes.create') }}"
                                       class="mt-4 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2 text-sm font-bold text-white hover:bg-violet-700">
                                        <i class="fa-solid fa-plus"></i> Thêm suất chiếu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($showtimes->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $showtimes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
