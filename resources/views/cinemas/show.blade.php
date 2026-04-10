@extends('layouts.app')

@section('content')
@php
    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate);
@endphp

<div class="bg-gray-950 min-h-screen text-white">
    <section class="border-b border-gray-800 bg-[radial-gradient(circle_at_top_right,_rgba(220,38,38,0.22),_transparent_34%),linear-gradient(180deg,_rgba(17,24,39,0.98),_rgba(3,7,18,1))]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <a href="{{ route('cinemas.index') }}" class="inline-flex items-center text-sm text-gray-400 hover:text-white transition-colors mb-6">
                <i class="fa-solid fa-arrow-left mr-2 text-xs"></i>
                Quay lại danh sách rạp
            </a>

            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-red-400 mb-3">Cinema Schedule</p>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">{{ $cinema->name }}</h1>
                    <p class="text-lg text-gray-300 leading-relaxed">{{ $cinema->address ?: 'Địa chỉ đang cập nhật.' }}</p>
                </div>

                <div class="rounded-2xl border border-gray-800 bg-gray-900/80 px-5 py-4 text-sm text-gray-300">
                    <div class="text-gray-500 uppercase tracking-[0.25em] text-xs mb-2">Ngày đang xem</div>
                    <div class="text-xl font-bold text-white">{{ ucfirst($selectedDateCarbon->translatedFormat('l')) }}</div>
                    <div class="text-red-400 font-semibold">{{ $selectedDateCarbon->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-3 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ($availableDates as $date)
                @php
                    $isActive = $date->toDateString() === $selectedDate;
                @endphp
                <a
                    href="{{ route('cinemas.show', ['cinema' => $cinema->id, 'date' => $date->toDateString()]) }}"
                    class="flex-shrink-0 min-w-24 rounded-2xl border px-4 py-3 text-center transition {{ $isActive ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-500/20' : 'border-gray-800 bg-gray-900 text-gray-300 hover:border-gray-600 hover:text-white' }}"
                >
                    <div class="text-xs uppercase tracking-[0.22em] {{ $isActive ? 'text-red-100' : 'text-gray-500' }}">
                        {{ $date->translatedFormat('D') }}
                    </div>
                    <div class="text-2xl font-black leading-none mt-2">{{ $date->format('d') }}</div>
                    <div class="text-xs mt-2 {{ $isActive ? 'text-red-100' : 'text-gray-500' }}">
                        {{ $date->translatedFormat('m/Y') }}
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if ($movies->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-800 bg-gray-900/80 px-8 py-16 text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gray-800 text-gray-400">
                    <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-3">Chưa có suất chiếu cho ngày này</h2>
                <p class="text-gray-400 max-w-2xl mx-auto">
                    Hãy chọn một ngày khác trên thanh lịch phía trên. Khi có showtime, mỗi phim sẽ hiển thị poster, mô tả ngắn và toàn bộ giờ chiếu để bạn đi tiếp tới trang chọn ghế.
                </p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($movies as $movie)
                    <article class="overflow-hidden rounded-3xl border border-gray-800 bg-gray-900/85">
                        <div class="flex flex-col lg:flex-row">
                            <div class="lg:w-60 xl:w-72">
                                <img
                                    src="{{ $movie->poster ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=600&h=900&auto=format&fit=crop' }}"
                                    alt="{{ $movie->name }}"
                                    class="h-72 w-full object-cover lg:h-full"
                                >
                            </div>

                            <div class="flex-1 p-6 lg:p-8">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white">
                                        {{ $movie->age_limit ? 'T' . $movie->age_limit : 'P' }}
                                    </span>
                                    @if ($movie->genre)
                                        <span class="rounded-full border border-gray-700 px-3 py-1 text-xs font-semibold text-gray-300">
                                            {{ $movie->genre }}
                                        </span>
                                    @endif
                                    @if ($movie->duration)
                                        <span class="text-sm text-gray-400">
                                            <i class="fa-regular fa-clock mr-1"></i>{{ $movie->duration }} phút
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-3xl font-black tracking-tight text-white mb-3">{{ $movie->name }}</h2>
                                <p class="text-gray-300 leading-relaxed mb-8">
                                    {{ \Illuminate\Support\Str::limit($movie->description ?: 'Nội dung phim đang được cập nhật.', 180) }}
                                </p>

                                <div>
                                    <div class="flex items-center justify-between gap-4 mb-4">
                                        <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-gray-400">Suất chiếu</h3>
                                        <span class="text-sm text-gray-500">{{ count($movie->showtimes) }} khung giờ</span>
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($movie->showtimes as $showtime)
                                            <a
                                                href="{{ route('booking.show', $showtime->id) }}"
                                                class="rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-left transition hover:border-red-500 hover:text-red-400"
                                            >
                                                <div class="text-lg font-bold text-white">{{ $showtime->time }}</div>
                                                <div class="text-xs text-gray-400 mt-1">{{ $showtime->room_name }}</div>
                                                <div class="text-xs text-red-400 mt-1">
                                                    {{ $showtime->subtitle_name ?: 'Đang cập nhật phụ đề' }}
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
