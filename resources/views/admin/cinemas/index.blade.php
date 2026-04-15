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
                <h2 class="text-2xl font-extrabold tracking-tight text-white">Danh sách Rạp Chiếu</h2>
                <p class="mt-1 text-sm text-gray-400">Quản lý toàn bộ cụm rạp và phòng chiếu trong hệ thống.</p>
            </div>
        </div>
        <a href="{{ route('admin.cinemas.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-sky-900/30 transition hover:bg-sky-700">
            <i class="fa-solid fa-plus"></i> Thêm Rạp Mới
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-400 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/70 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-gray-800 bg-gray-950/50 uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">#</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Tên Rạp</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Địa chỉ</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-center">Phòng chiếu</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($cinemas as $cinema)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-gray-500">{{ $cinema->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div class="font-bold text-white">{{ $cinema->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400 max-w-xs truncate">
                                {{ $cinema->address ?? '— Chưa có địa chỉ' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="rounded-full bg-sky-500/10 px-3 py-1 text-xs font-bold text-sky-400">
                                    {{ $cinema->rooms_count }} phòng
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right border-l border-gray-800/50">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.cinemas.edit', $cinema) }}"
                                       class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400 transition hover:bg-sky-500/20"
                                       title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.cinemas.destroy', $cinema) }}" method="POST"
                                          onsubmit="return confirm('Xóa rạp {{ $cinema->name }}? Tất cả phòng chiếu liên quan cũng sẽ bị xóa!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/10 text-red-500 transition hover:bg-red-500/20"
                                                title="Xóa rạp">
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
                                    <i class="fa-solid fa-building text-4xl mb-3 text-gray-700"></i>
                                    <p class="font-semibold">Chưa có rạp nào trong hệ thống.</p>
                                    <a href="{{ route('admin.cinemas.create') }}"
                                       class="mt-4 inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-bold text-white hover:bg-sky-700">
                                        <i class="fa-solid fa-plus"></i> Thêm rạp ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($cinemas->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $cinemas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
