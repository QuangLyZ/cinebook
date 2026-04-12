@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">Danh sách Phim</h2>
            <p class="mt-1 text-sm text-gray-400">Quản lý toàn bộ thư viện phim chiếu rạp của hệ thống.</p>
        </div>
        <a href="{{ route('admin.movies.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-900/30 transition hover:bg-red-700">
            <i class="fa-solid fa-plus"></i>
            Thêm Phim Mới
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-400">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">Phim</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Thể loại</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Thời lượng</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Ngày Chiếu</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($movies as $movie)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-16 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-800">
                                        @if($movie->poster)
                                            <img src="{{ $movie->poster }}" alt="{{ $movie->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-gray-600">
                                                <i class="fa-solid fa-film"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ $movie->name }}</div>
                                        <div class="mt-1 text-xs text-gray-500">Đạo diễn: {{ $movie->director ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-300">
                                    {{ $movie->genre ?? 'Chưa rập' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-300">
                                {{ $movie->duration ? $movie->duration . ' phút' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-300">
                                {{ $movie->release_date ? \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right border-l border-gray-800/50">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.movies.edit', $movie) }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400 transition hover:bg-sky-500/20" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.movies.destroy', $movie) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phim này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/10 text-red-500 transition hover:bg-red-500/20" title="Xóa phim">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-film text-4xl mb-3 text-gray-700"></i>
                                    <p>Chưa có phim nào trong hệ thống.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($movies->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $movies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
