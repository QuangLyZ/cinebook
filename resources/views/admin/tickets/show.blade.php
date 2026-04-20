@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
<div class="max-w-4xl space-y-6 animate-[fadeIn_0.5s_ease-in-out]">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tickets.index') }}"
               class="text-[rgb(255,255,255)] transition hover:text-gray-300">
                <i class="fa-solid fa-chevron-left text-2xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-white">
                    Chi tiết Vé {{ $ticket->ticket_code ?: ('CB-' . str_pad((string) $ticket->id, 8, '0', STR_PAD_LEFT)) }}
                </h2>
                <p class="mt-1 text-sm text-gray-400">Xem thông tin ghế và thanh toán chi tiết.</p>
            </div>
        </div>
        <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST"
              onsubmit="return confirm('Bạn có thực sự muốn xóa vé này? Hành động này không thể hoàn tác!');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-500/30 bg-red-500/10 px-5 py-2.5 text-sm font-bold text-red-500 transition hover:bg-red-500/20">
                <i class="fa-solid fa-trash-can"></i> Xóa Vé
            </button>
        </form>
    </div>

    <div class="grid gap-6 md:grid-cols-2">

        {{-- Ticket & Showtime Info --}}
        <div class="rounded-3xl border border-gray-800 bg-gray-900/80 p-6 space-y-6 shadow-xl">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500/10 text-sky-400">
                    <i class="fa-solid fa-film text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Thông tin Suất Chiếu</h3>
                    <p class="text-sm text-gray-400">Phim, Rạp và Lịch chiếu</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="text-sm font-semibold text-gray-500">Phim:</div>
                    <div class="mt-1 text-lg font-bold text-white">{{ $ticket->showtime?->movie?->name ?? 'N/A' }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-500">Cụm Rạp:</div>
                        <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->showtime?->room?->cinema?->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-500">Phòng Chiếu:</div>
                        <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->showtime?->room?->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-500">Thời gian chiếu:</div>
                        <div class="mt-1 text-base font-semibold text-sky-400">{{ $ticket->showtime?->start_time?->format('H:i d/m/Y') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-500">Ngôn ngữ:</div>
                        <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->showtime?->subtitle?->name ?? 'Dịch máy' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="rounded-3xl border border-gray-800 bg-gray-900/80 p-6 space-y-6 shadow-xl">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400">
                    <i class="fa-solid fa-user-astronaut text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Khách hàng</h3>
                    <p class="text-sm text-gray-400">Thông tin người đặt vé</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="text-sm font-semibold text-gray-500">Tên người nhận vé:</div>
                    <div class="mt-1 text-lg font-bold text-white">{{ $ticket->fullname ?? ($ticket->user?->name ?? 'Khách viếng thăm') }}</div>
                </div>
                
                <div>
                    <div class="text-sm font-semibold text-gray-500">Email:</div>
                    <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->email ?? ($ticket->user?->email ?? 'N/A') }}</div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-500">Số điện thoại:</div>
                    <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->phone ?? 'N/A' }}</div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-500">Ngày đặt vé:</div>
                    <div class="mt-1 text-base font-semibold text-gray-200">{{ $ticket->booking_date?->format('H:i d/m/Y') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        {{-- Seats & Payment --}}
        <div class="md:col-span-2 rounded-3xl border border-gray-800 bg-gray-900/80 p-6 shadow-xl">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-4 mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400">
                    <i class="fa-solid fa-couch text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Chi tiết ghế & Thanh toán</h3>
                    <p class="text-sm text-gray-400">Danh sách ghế ngồi và tổng tiền</p>
                </div>
            </div>

            @if($ticket->details->count() > 0)
                <div class="overflow-hidden rounded-2xl border border-gray-800">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-950/50 uppercase text-gray-400 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Tên ghế</th>
                                <th class="px-6 py-4 font-semibold">Loại ghế</th>
                                <th class="px-6 py-4 font-semibold text-right">Giá gốc (bán)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($ticket->details as $detail)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-white">{{ $detail->seat?->seat_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-300">
                                        <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold capitalize text-gray-200">
                                            {{ $detail->seat?->seat_type ?? 'Standard' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-300">
                                        {{ number_format((float)$detail->price_at_booking, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-950 border-t border-gray-800">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right font-semibold text-gray-400 uppercase tracking-wider">Tổng cộng:</td>
                                <td class="px-6 py-4 text-right text-xl font-extrabold text-amber-400">
                                    {{ number_format((float)$ticket->total_price, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-700 bg-gray-950/50 p-8 text-center text-gray-500">
                    Chưa có dữ liệu ghế cho vé này.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
