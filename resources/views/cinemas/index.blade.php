@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-white">
    <section class="border-b border-gray-800 bg-[radial-gradient(circle_at_top_left,_rgba(220,38,38,0.18),_transparent_38%),linear-gradient(180deg,_rgba(17,24,39,0.98),_rgba(3,7,18,1))]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="max-w-3xl">
                <p class="text-sm uppercase tracking-[0.3em] text-red-400 mb-4">Cinema Directory</p>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-5">Danh sách rạp</h1>
                <p class="text-lg text-gray-300 leading-relaxed">
                    Chọn rạp gần bạn để xem lịch chiếu theo ngày và đi thẳng tới bước đặt ghế cho suất chiếu phù hợp.
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        @if ($cinemas->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-700 bg-gray-900/70 px-8 py-16 text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10 text-red-400">
                    <i class="fa-solid fa-film text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-3">Chưa có dữ liệu rạp</h2>
                <p class="text-gray-400 max-w-xl mx-auto">
                    Cinebook sẽ cập nhật dữ liệu rạp chiếu sớm nhất có thể. Hãy quay lại sau để khám phá các rạp chiếu gần bạn và đặt vé nhanh chóng!
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($cinemas as $cinema)
                    <a
                        href="{{ route('cinemas.show', $cinema->id) }}"
                        class="group rounded-3xl border border-gray-800 bg-gray-900/80 p-7 transition duration-300 hover:-translate-y-1 hover:border-red-500/60 hover:bg-gray-900"
                    >
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-400 transition group-hover:bg-red-500 group-hover:text-white">
                                <i class="fa-solid fa-location-dot text-lg"></i>
                            </div>
                            <span class="text-xs uppercase tracking-[0.28em] text-gray-500">Chi tiết</span>
                        </div>

                        <h2 class="text-2xl font-bold text-white mb-3 leading-tight">{{ $cinema->name }}</h2>
                        <p class="text-gray-400 leading-relaxed min-h-16">{{ $cinema->address ?: 'Địa chỉ đang cập nhật.' }}</p>

                        <div class="mt-8 inline-flex items-center text-sm font-semibold text-red-400">
                            Xem lịch chiếu
                            <i class="fa-solid fa-arrow-right ml-2 text-xs transition-transform group-hover:translate-x-1"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
