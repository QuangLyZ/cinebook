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
                <h2 class="text-2xl font-extrabold tracking-tight text-white">Quản lý Vé</h2>
                <p class="mt-1 text-sm text-gray-400">Theo dõi lịch sử đặt vé và chi tiết giao dịch của người dùng.</p>
            </div>
        </div>
        {{-- CSV Export --}}
        <a href="{{ route('admin.tickets.export', array_filter($filters ?? [])) }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-700">
            <i class="fa-solid fa-file-csv"></i> Xuất CSV
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
    <form method="GET" action="{{ route('admin.tickets.index') }}"
          class="flex flex-wrap gap-3 rounded-2xl border border-gray-800 bg-gray-900/60 px-5 py-4">
        <div class="relative flex-1 min-w-48">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                   placeholder="Tìm tên, email, SĐT..."
                   class="w-full rounded-xl border border-gray-700 bg-black/40 py-2.5 pl-10 pr-4 text-sm text-white placeholder-gray-500 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
        </div>
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
               class="rounded-xl border border-gray-700 bg-black/40 px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-700">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>
        @if(array_filter($filters ?? []))
            <a href="{{ route('admin.tickets.index') }}"
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
                        <th class="px-6 py-4 font-semibold tracking-wider">#</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Phim / Suất</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Rạp</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Tổng tiền</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Ngày đặt</th>
                        <th class="px-6 py-4 font-semibold tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($tickets as $ticket)
                        <tr class="transition-colors hover:bg-gray-800/50">
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">#{{ $ticket->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-white">
                                    {{ $ticket->fullname ?? ($ticket->user?->name ?? 'Guest') }}
                                </div>
                                <div class="mt-0.5 text-xs text-gray-500">
                                    {{ $ticket->email ?? $ticket->user?->email }}
                                </div>
                                @if($ticket->phone)
                                    <div class="mt-0.5 text-xs text-gray-500">{{ $ticket->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-200">
                                    {{ $ticket->showtime?->movie?->name ?? '—' }}
                                </div>
                                <div class="mt-0.5 text-xs text-gray-500">
                                    {{ $ticket->showtime?->start_time?->format('H:i d/m/Y') ?? '—' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-300">{{ $ticket->showtime?->room?->cinema?->name ?? '—' }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $ticket->showtime?->room?->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-amber-400">
                                    {{ number_format((float)$ticket->total_price, 0, ',', '.') }}đ
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $ticket->booking_date?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right border-l border-gray-800/50">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}"
                                       class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 transition hover:bg-amber-500/20"
                                       title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST"
                                          onsubmit="return confirm('Xóa vé #{{ $ticket->id }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/10 text-red-500 transition hover:bg-red-500/20"
                                                title="Xóa vé">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-ticket text-4xl mb-3 text-gray-700"></i>
                                    <p class="font-semibold">Chưa có vé nào được đặt.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="border-t border-gray-800 px-6 py-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
