@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
    @if ($activeTab === 'feedback')
        <div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-white md:text-4xl">Ý kiến phản hồi từ khách hàng</h2>
                    <p class="mt-3 text-gray-400">Xem và phản hồi trực tiếp qua email các góp ý từ người dùng.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-400 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-red-400 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($feedbacks->isEmpty())
                <div class="flex min-h-[400px] flex-col items-center justify-center rounded-[2rem] border border-dashed border-gray-800 bg-gray-900/40 p-12 text-center">
                    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-800 text-gray-500 shadow-inner">
                        <i class="fa-solid fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Hộp thư trống</h3>
                    <p class="mt-2 text-gray-400">Chưa có phản hồi nào từ khách hàng.</p>
                </div>
            @else
                <div class="grid gap-6">
                    @foreach($feedbacks as $item)
                        <div class="group relative overflow-hidden rounded-[2rem] border border-gray-800 bg-gray-900/60 p-6 shadow-lg backdrop-blur-sm transition duration-300 hover:border-red-500/30 hover:bg-gray-900/80">
                            <div class="flex flex-col gap-6 md:flex-row md:items-start">
                                <!-- User Info Avatar -->
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 text-xl font-bold text-white shadow-inner ring-1 ring-gray-700/50 group-hover:from-red-900/50 group-hover:to-red-800/30 group-hover:text-red-400 transition-all">
                                    {{ mb_strtoupper(mb_substr($item->user->name ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') }}
                                </div>

                                <div class="flex-1 space-y-5 w-full">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h4 class="text-xl font-extrabold text-white group-hover:text-red-400 transition">{{ $item->title }}</h4>
                                            <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm text-gray-400">
                                                <span class="font-bold text-gray-200 uppercase tracking-wider text-xs">{{ $item->user->name ?? 'Người dùng ẩn danh' }}</span>
                                                <span class="h-1 w-1 rounded-full bg-gray-600"></span>
                                                <span>{{ $item->user->email ?? 'Không có email' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-left sm:text-right">
                                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $item->created_at->format('H:i d/m/Y') }}</div>
                                            <div class="mt-1 text-xs text-red-500/80">{{ $item->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-black/30 p-5 text-gray-300 leading-relaxed border border-gray-800/40 font-mono text-sm shadow-inner">
                                        {{ $item->context }}
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 pt-2">
                                        <button onclick="toggleReplyForm({{ $item->id }})" class="inline-flex items-center gap-2 rounded-xl bg-gray-800 px-5 py-2.5 text-xs font-bold text-gray-200 shadow-sm transition hover:bg-gray-700 hover:text-white ring-1 ring-gray-700">
                                            <i class="fa-solid fa-reply"></i>
                                            Trả lời qua Email
                                        </button>
                                        
                                        <form action="{{ route('admin.feedback.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-transparent px-5 py-2.5 text-xs font-bold text-gray-400 transition hover:bg-red-500/10 hover:text-red-400 ring-1 ring-gray-800 hover:ring-red-500/30">
                                                <i class="fa-solid fa-trash-can"></i>
                                                Xoá
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Reply Form (Hidden by default) -->
                                    <div id="reply-form-{{ $item->id }}" class="hidden mt-4 pt-6 border-t border-gray-800/60">
                                        <form action="{{ route('admin.feedback.reply', $item->id) }}" method="POST" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-300 mb-2">Nội dung trả lời tới <span class="text-white">{{ $item->user->email ?? 'Khách' }}</span>:</label>
                                                <textarea name="reply_message" rows="4" required class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-sm text-white placeholder-gray-600 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20" placeholder="Viết phản hồi của bạn ở đây..."></textarea>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" onclick="toggleReplyForm({{ $item->id }})" class="rounded-xl border border-gray-700 px-4 py-2 text-xs font-bold text-gray-400 hover:bg-gray-800 hover:text-white transition">Hủy</button>
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-red-900/20 hover:bg-red-700 transition">
                                                    <i class="fa-solid fa-paper-plane"></i> Gửi Email
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <script>
            function toggleReplyForm(id) {
                const form = document.getElementById('reply-form-' + id);
                if (form.classList.contains('hidden')) {
                    form.classList.remove('hidden');
                    form.classList.add('animate-[fadeIn_0.3s_ease-in-out]');
                } else {
                    form.classList.add('hidden');
                    form.classList.remove('animate-[fadeIn_0.3s_ease-in-out]');
                }
            }
        </script>

    @elseif ($activeTab === 'dashboard')
        @php
            $filter = $dashboardFilter ?? 'day';
            $deltaTone = fn ($tone) => match ($tone) {
                'up' => 'text-emerald-400',
                'down' => 'text-red-400',
                default => 'text-gray-400',
            };
            $deltaIcon = fn ($tone) => match ($tone) {
                'up' => 'fa-caret-up',
                'down' => 'fa-caret-down',
                default => 'fa-minus',
            };
        @endphp

        <section class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Cinema Intelligence</div>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-white">Dashboard điều hành cụm rạp</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">
                        Tổng hợp số liệu theo {{ strtolower($dashboardFilterLabel ?? 'ngày') }} cho giai đoạn {{ $dashboardWindowLabel ?? '' }}.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach (['day' => 'Ngày', 'week' => 'Tuần', 'month' => 'Tháng'] as $rangeKey => $rangeLabel)
                        <a
                            href="{{ route('admin.dashboard', ['range' => $rangeKey]) }}"
                            class="{{ $filter === $rangeKey ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-950/30' : 'border-gray-800 bg-gray-900/80 text-gray-300 hover:border-gray-700 hover:text-white' }} inline-flex items-center rounded-2xl border px-4 py-2.5 text-sm font-semibold transition"
                        >
                            {{ $rangeLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach (($summaryMetrics ?? []) as $metric)
                    <article class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">{{ $metric['label'] }}</div>
                                <div class="mt-4 text-3xl font-extrabold text-white">{{ $metric['value'] }}</div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-300">
                                <i class="fa-solid {{ $metric['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold {{ $deltaTone($metric['delta']['tone']) }}">
                            <i class="fa-solid {{ $deltaIcon($metric['delta']['tone']) }}"></i>
                            <span>{{ $metric['delta']['text'] }}</span>
                            <span class="text-gray-500">so với cùng kỳ</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="grid gap-6">
                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Chi tiết</div>
                            <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Doanh thu theo {{ strtolower($dashboardFilterLabel ?? 'ngày') }}</h3>
                        </div>
                        <div class="text-sm text-gray-500">{{ $dashboardWindowLabel ?? '' }}</div>
                    </div>

                    <div class="mt-8">
                        <div class="dashboard-chart-scroll overflow-x-auto pb-[clamp(0.75rem,1.5vw,1rem)]">
                            <div class="dashboard-chart-track flex min-w-full items-end gap-[clamp(0.35rem,0.9vw,0.75rem)]">
                                @foreach (($detailMetrics['revenue_chart'] ?? collect()) as $point)
                                    <div class="dashboard-chart-bar flex min-w-[clamp(3.5rem,8%,5.5rem)] flex-1 shrink-0 basis-[8%] flex-col items-center gap-[clamp(0.4rem,1vw,0.75rem)]">
                                        <div class="max-w-full text-center text-[clamp(0.6rem,1.2vw,0.78rem)] leading-[1.35] text-gray-500">{{ $point->value }}</div>
                                        <div class="flex h-[clamp(12rem,32vw,18rem)] w-[92%] items-end rounded-[clamp(1rem,2vw,1.5rem)] bg-gray-950/50 px-[clamp(0.18rem,0.6vw,0.4rem)]">
                                            <div class="w-full rounded-t-[clamp(0.85rem,1.8vw,1.35rem)] bg-gradient-to-t from-red-600 via-red-500 to-amber-400" style="height: {{ $point->height }}%"></div>
                                        </div>
                                        <div class="w-full text-center text-[clamp(0.68rem,1.3vw,0.85rem)] font-semibold text-gray-400">{{ $point->label }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Top phim</div>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Được xem nhiều nhất</h3>
                        <div class="mt-6 space-y-4">
                            @forelse (($detailMetrics['top_movies'] ?? collect()) as $item)
                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-950 text-xs font-bold text-red-300">{{ $item->rank }}</span>
                                            <span class="font-semibold text-white">{{ $item->label }}</span>
                                        </div>
                                        <span class="text-sm text-gray-400">{{ $item->value }}</span>
                                    </div>
                                    <div class="h-2.5 rounded-full bg-gray-950">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-red-500 to-amber-400" style="width: {{ $item->width }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Chưa có dữ liệu vé trong kỳ này.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Top rạp</div>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Đông khách nhất</h3>
                        <div class="mt-6 space-y-4">
                            @forelse (($detailMetrics['top_cinemas'] ?? collect()) as $item)
                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-950 text-xs font-bold text-sky-300">{{ $item->rank }}</span>
                                            <span class="font-semibold text-white">{{ $item->label }}</span>
                                        </div>
                                        <span class="text-sm text-gray-400">{{ $item->value }}</span>
                                    </div>
                                    <div class="h-2.5 rounded-full bg-gray-950">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-sky-500 to-cyan-300" style="width: {{ $item->width }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Chưa có dữ liệu vé trong kỳ này.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Hoạt động</div>
                            <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Suất đang chiếu và tỷ lệ fill</h3>
                        </div>
                        <div class="text-sm text-gray-500">{{ ($liveShowtimes ?? collect())->count() }} suất</div>
                    </div>
                    <div class="mt-6 space-y-4">
                        @forelse (($liveShowtimes ?? collect()) as $showtime)
                            <article class="rounded-[1.5rem] border border-gray-800 bg-gray-950/70 p-4">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <div class="text-lg font-bold text-white">{{ $showtime->movie_name }}</div>
                                        <div class="mt-1 text-sm text-gray-400">{{ $showtime->cinema_name }} • {{ $showtime->room_name }}</div>
                                        <div class="mt-2 text-xs uppercase tracking-[0.18em] text-gray-500">
                                            {{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="min-w-[220px]">
                                        <div class="mb-2 flex items-center justify-between text-sm">
                                            <span class="text-gray-400">{{ $showtime->sold_seats }}/{{ $showtime->seat_count }} ghế</span>
                                            <span class="font-bold text-white">{{ number_format($showtime->fill_rate, 1) }}%</span>
                                        </div>
                                        <div class="h-3 rounded-full bg-black/40">
                                            <div class="h-3 rounded-full bg-gradient-to-r from-emerald-500 via-yellow-400 to-red-500" style="width: {{ min($showtime->fill_rate, 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-gray-800 bg-gray-950/40 p-10 text-center text-gray-500">
                                Hiện chưa có suất nào đang chiếu ở thời điểm này.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Quick Action</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Lối tắt thao tác</h3>
                    <div class="mt-6 grid gap-4">
                        @foreach (($quickActions ?? []) as $action)
                            @if ($action['href'])
                                <a href="{{ $action['href'] }}" class="rounded-[1.5rem] border border-gray-800 bg-gray-950/80 p-4 transition hover:border-red-500/30 hover:bg-gray-900">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white">
                                            <i class="fa-solid {{ $action['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $action['label'] }}</div>
                                            <div class="mt-1 text-sm leading-6 text-gray-400">{{ $action['description'] }}</div>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="rounded-[1.5rem] border border-gray-800 bg-gray-950/60 p-4 opacity-80">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-800 text-gray-300">
                                            <i class="fa-solid {{ $action['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $action['label'] }}</div>
                                            <div class="mt-1 text-sm leading-6 text-gray-400">{{ $action['description'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10 xl:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Khách hàng</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Chỉ số khách hàng</h3>

                    <div class="mt-6 space-y-5">
                        <div class="rounded-[1.5rem] border border-gray-800 bg-gray-950/70 p-4">
                            <div class="text-sm text-gray-400">{{ $customerMetrics['new_users']['label'] ?? '' }}</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $customerMetrics['new_users']['value'] ?? '0' }}</div>
                            <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold {{ $deltaTone($customerMetrics['new_users']['delta']['tone'] ?? 'neutral') }}">
                                <i class="fa-solid {{ $deltaIcon($customerMetrics['new_users']['delta']['tone'] ?? 'neutral') }}"></i>
                                <span>{{ $customerMetrics['new_users']['delta']['text'] ?? '0%' }}</span>
                            </div>
                        </div>

                        <div class="rounded-[1.5rem] border border-gray-800 bg-gray-950/70 p-4">
                            <div class="text-sm text-gray-400">{{ $customerMetrics['turnover_rate']['label'] ?? '' }}</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $customerMetrics['turnover_rate']['value'] ?? '0%' }}</div>
                            <div class="mt-2 text-sm text-gray-500">{{ $customerMetrics['turnover_rate']['subtext'] ?? '' }}</div>
                            <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold {{ $deltaTone($customerMetrics['turnover_rate']['delta']['tone'] ?? 'neutral') }}">
                                <i class="fa-solid {{ $deltaIcon($customerMetrics['turnover_rate']['delta']['tone'] ?? 'neutral') }}"></i>
                                <span>{{ $customerMetrics['turnover_rate']['delta']['text'] ?? '0%' }}</span>
                            </div>
                        </div>

                        <div class="rounded-[1.5rem] border border-gray-800 bg-gray-950/70 p-4">
                            <div class="text-sm text-gray-400">{{ $customerMetrics['popular_booking_hour']['label'] ?? '' }}</div>
                            <div class="mt-3 text-2xl font-extrabold text-white">{{ $customerMetrics['popular_booking_hour']['value'] ?? '--:--' }}</div>
                            <div class="mt-2 text-sm text-gray-500">{{ $customerMetrics['popular_booking_hour']['subtext'] ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10 xl:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Khách hàng</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Giờ đặt vé phổ biến</h3>
                    <div class="mt-6 space-y-3">
                        @foreach (($customerMetrics['hour_distribution'] ?? collect()) as $slot)
                            <div class="flex items-center gap-3">
                                <div class="w-10 text-xs font-semibold text-gray-500">{{ $slot->label }}</div>
                                <div class="h-2.5 flex-1 rounded-full bg-gray-950">
                                    <div class="h-2.5 rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-400" style="width: {{ $slot->width }}%"></div>
                                </div>
                                <div class="w-10 text-right text-xs text-gray-400">{{ $slot->total }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10 xl:col-span-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Thanh toán</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Hiệu suất thanh toán</h3>

                    <div class="mt-6 grid gap-4">
                        @foreach (['success', 'failed'] as $paymentKey)
                            @php $payment = $paymentMetrics[$paymentKey] ?? null; @endphp
                            @if ($payment)
                                <div class="rounded-[1.5rem] border border-gray-800 bg-gray-950/70 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="text-sm text-gray-400">{{ $payment['label'] }}</div>
                                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $payment['value'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="{{ $paymentKey === 'success' ? 'text-emerald-400' : 'text-red-400' }} text-xl font-bold">{{ number_format($payment['rate'], 1) }}%</div>
                                        </div>
                                    </div>
                                    <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold {{ $deltaTone($payment['delta']['tone']) }}">
                                        <i class="fa-solid {{ $deltaIcon($payment['delta']['tone']) }}"></i>
                                        <span>{{ $payment['delta']['text'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>

            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Promo</div>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Hiệu quả voucher</h3>
                    </div>
                    <div class="text-sm text-gray-500">Theo {{ strtolower($dashboardFilterLabel ?? 'ngày') }}</div>
                </div>

                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800 text-sm">
                            <thead class="bg-gray-950/80 text-left text-xs uppercase tracking-[0.22em] text-gray-500">
                                <tr>
                                    <th class="px-4 py-4">Mã voucher</th>
                                    <th class="px-4 py-4">Số lần dùng</th>
                                    <th class="px-4 py-4">Giảm giá đã áp</th>
                                    <th class="px-4 py-4">Doanh thu mang về</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 bg-gray-950/40">
                                @forelse (($promoMetrics ?? collect()) as $promo)
                                    <tr class="align-top text-gray-300">
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-white">{{ $promo->code }}</div>
                                            <div class="mt-2 h-2.5 rounded-full bg-black/40">
                                                <div class="h-2.5 rounded-full bg-gradient-to-r from-amber-400 to-red-500" style="width: {{ $promo->usage_width }}%"></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">{{ number_format($promo->usages) }}</td>
                                        <td class="px-4 py-4">{{ $promo->discount }}</td>
                                        <td class="px-4 py-4 font-semibold text-white">{{ $promo->revenue }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">Chưa có voucher nào được sử dụng trong kỳ này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    @elseif ($activeTab === 'posts')
        <div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-300">
                        <i class="fa-solid fa-newspaper"></i>
                        Content Hub
                    </div>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-white md:text-4xl">Bài viết gần đây</h2>
                    <p class="mt-2 text-gray-400">Danh sách bài viết để admin theo dõi nhanh trước khi vào khu quản lý chi tiết.</p>
                </div>
                <a href="{{ route('admin.posts.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    Mở quản lý bài viết
                </a>
            </div>

            <div class="grid gap-4">
                @forelse(($posts ?? collect()) as $post)
                    <article class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10 transition hover:border-gray-700">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $post->title }}</h3>
                                <p class="mt-2 text-sm text-gray-400">{{ $post->keywords ?: 'Chưa có từ khóa' }}</p>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $post->publish_at ?? 'Đăng ngay' }}
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-gray-700 bg-gray-900/40 px-6 py-14 text-center text-gray-400">
                        Chưa có bài viết nào để hiển thị.
                    </div>
                @endforelse
            </div>
        </div>

    @elseif ($activeTab === 'actions')
        @php
            $vouchers = $vouchers ?? collect();
            $editingVoucher = $editingVoucher ?? null;
            $voucherStats = $voucherStats ?? ['total' => 0, 'active' => 0, 'expired' => 0, 'usage_cap' => 0];
            $voucherFilters = $voucherFilters ?? ['q' => '', 'status' => ''];
            $discountType = old('discount_type');

            if ($discountType === null && $editingVoucher) {
                $discountType = $editingVoucher->discount_rate ? 'rate' : 'value';
            }

            $discountType = $discountType ?: 'value';
        @endphp

        <section class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
            @if (session('success'))
                <div class="rounded-[1.75rem] border border-emerald-500/20 bg-emerald-500/10 px-5 py-4 text-sm font-semibold text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[1.75rem] border border-red-500/20 bg-red-500/10 px-5 py-4">
                    <div class="text-sm font-semibold text-red-200">Có dữ liệu voucher chưa hợp lệ.</div>
                    <ul class="mt-3 space-y-2 text-sm text-red-100/90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Total</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $voucherStats['total'] }}</div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/15 text-red-300">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Active</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $voucherStats['active'] }}</div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-300">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Expired</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $voucherStats['expired'] }}</div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-300">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Usage Limit</div>
                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $voucherStats['usage_cap'] }}</div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-500/15 text-sky-300">
                            <i class="fa-solid fa-gauge-high"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_420px]">
                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 shadow-lg shadow-black/10">
                    <div class="flex flex-col gap-4 border-b border-gray-800 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Voucher Registry</div>
                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Quản lý voucher tại tab Action</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-400">Giữ cùng tinh thần bảng quản trị: tìm nhanh, xem trạng thái và thao tác trực tiếp trên từng dòng.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.actions') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_170px_auto]">
                            <label class="flex items-center gap-3 rounded-2xl border border-gray-800 bg-gray-950/80 px-4 py-3">
                                <i class="fa-solid fa-magnifying-glass text-gray-500"></i>
                                <input type="text" name="q" value="{{ $voucherFilters['q'] }}" placeholder="Tìm theo code hoặc mô tả" class="w-full border-0 bg-transparent p-0 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-0">
                            </label>
                            <select name="status" class="rounded-2xl border border-gray-800 bg-gray-950/80 px-4 py-3 text-sm text-white focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ $voucherFilters['status'] === 'active' ? 'selected' : '' }}>Đang bật</option>
                                <option value="inactive" {{ $voucherFilters['status'] === 'inactive' ? 'selected' : '' }}>Đã tắt</option>
                            </select>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                                <i class="fa-solid fa-filter"></i>
                                Lọc
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800 text-sm">
                            <thead class="bg-black/20 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
                                <tr>
                                    <th class="px-6 py-4">Code</th>
                                    <th class="px-6 py-4">Giảm giá</th>
                                    <th class="px-6 py-4">Thời gian</th>
                                    <th class="px-6 py-4">Giới hạn</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @forelse ($vouchers as $voucher)
                                    @php
                                        $isExpired = $voucher->expires_at && $voucher->expires_at->isPast();
                                    @endphp
                                    <tr class="bg-transparent transition hover:bg-gray-950/50">
                                        <td class="px-6 py-5 align-top">
                                            <div class="font-bold text-white">{{ $voucher->code }}</div>
                                            <div class="mt-1 max-w-xs text-xs leading-5 text-gray-500">{{ $voucher->description ?: 'Chưa có mô tả cho voucher này.' }}</div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-gray-300">
                                            @if (!is_null($voucher->discount_rate))
                                                <div class="font-semibold text-white">{{ $voucher->discount_rate }}%</div>
                                                <div class="mt-1 text-xs text-gray-500">Giảm theo phần trăm</div>
                                            @else
                                                <div class="font-semibold text-white">{{ number_format((float) $voucher->discount_value, 0, ',', '.') }}đ</div>
                                                <div class="mt-1 text-xs text-gray-500">Giảm tiền trực tiếp</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 align-top text-gray-300">
                                            <div>{{ optional($voucher->starts_at)->format('d/m/Y H:i') ?: 'Ngay khi tạo' }}</div>
                                            <div class="mt-1 text-xs text-gray-500">{{ optional($voucher->expires_at)->format('d/m/Y H:i') ?: 'Không giới hạn' }}</div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-gray-300">
                                            <div>{{ is_null($voucher->usage_limit) ? 'Không giới hạn' : $voucher->used_count . ' / ' . $voucher->usage_limit }}</div>
                                            <div class="mt-1 text-xs text-gray-500">Đã dùng: {{ $voucher->used_count }}</div>
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            @if (!$voucher->is_active)
                                                <span class="inline-flex rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-gray-300">Tắt</span>
                                            @elseif ($isExpired)
                                                <span class="inline-flex rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-300">Hết hạn</span>
                                            @else
                                                <span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-300">Đang bật</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.actions', array_filter(['edit' => $voucher->id, 'q' => $voucherFilters['q'], 'status' => $voucherFilters['status']])) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-700 bg-gray-950 text-gray-300 transition hover:border-red-500/40 hover:text-white">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" onsubmit="return confirm('Xóa voucher {{ $voucher->code }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-700 bg-gray-950 text-gray-300 transition hover:border-red-500/40 hover:text-white">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gray-800 text-gray-500">
                                                <i class="fa-solid fa-ticket text-2xl"></i>
                                            </div>
                                            <div class="mt-4 text-lg font-bold text-white">Chưa có voucher nào</div>
                                            <div class="mt-2 text-sm text-gray-500">Tạo voucher đầu tiên ở khối bên phải để bắt đầu quản lý ưu đãi.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (method_exists($vouchers, 'hasPages') && $vouchers->hasPages())
                        <div class="border-t border-gray-800 px-6 py-4">
                            {{ $vouchers->links() }}
                        </div>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">{{ $editingVoucher ? 'Edit Voucher' : 'New Voucher' }}</div>
                            <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">{{ $editingVoucher ? 'Cập nhật voucher' : 'Tạo voucher mới' }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-400">Form nằm cùng tab để admin không phải chuyển màn hình khi cần sửa nhanh.</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-300">
                            <i class="fa-solid {{ $editingVoucher ? 'fa-wand-magic-sparkles' : 'fa-plus' }}"></i>
                        </div>
                    </div>

                    <form method="POST" action="{{ $editingVoucher ? route('admin.vouchers.update', $editingVoucher) : route('admin.vouchers.store') }}" class="mt-6 space-y-5">
                        @csrf
                        @if ($editingVoucher)
                            @method('PUT')
                        @endif

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-300">Mã voucher</label>
                            <input type="text" name="code" value="{{ old('code', $editingVoucher->code ?? '') }}" placeholder="Ví dụ: SUMMER25" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-300">Loại giảm giá</label>
                                <select name="discount_type" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                                    <option value="value" {{ $discountType === 'value' ? 'selected' : '' }}>Giảm theo số tiền</option>
                                    <option value="rate" {{ $discountType === 'rate' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                                </select>
                            </div>
                            <label class="flex items-center gap-3 rounded-2xl border border-gray-800 bg-black/30 px-4 py-3">
                                <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-500" {{ old('is_active', $editingVoucher?->is_active ?? true) ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-white">Kích hoạt voucher ngay</span>
                            </label>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-300">Giảm theo tiền (VND)</label>
                                <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $editingVoucher->discount_value ?? '') }}" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-300">Giảm theo %</label>
                                <input type="number" min="1" max="100" name="discount_rate" value="{{ old('discount_rate', $editingVoucher->discount_rate ?? '') }}" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-300">Mô tả</label>
                            <textarea name="description" rows="4" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">{{ old('description', $editingVoucher->description ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-300">Bắt đầu áp dụng</label>
                                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($editingVoucher?->starts_at)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-300">Hết hạn</label>
                                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($editingVoucher?->expires_at)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-300">Giới hạn số lượt dùng</label>
                            <input type="number" min="0" name="usage_limit" value="{{ old('usage_limit', $editingVoucher->usage_limit ?? '') }}" placeholder="Để trống nếu không giới hạn" class="w-full rounded-2xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-800 pt-5">
                            @if ($editingVoucher)
                                <a href="{{ route('admin.actions') }}" class="inline-flex items-center gap-2 rounded-2xl border border-gray-700 bg-transparent px-5 py-3 text-sm font-semibold text-gray-300 transition hover:bg-gray-800 hover:text-white">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Hủy sửa
                                </a>
                            @endif
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $editingVoucher ? 'Lưu cập nhật' : 'Tạo voucher' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    @elseif ($activeTab === 'management')
        @php
            $modules = [
                [
                    'title'   => 'Quản lý Phim',
                    'desc'    => 'Thêm, sửa, xoá phim, poster và thông tin nội dung.',
                    'icon'    => 'fa-film',
                    'color'   => 'red',
                    'count_label' => 'Phim trong hệ thống',
                    'count'   => \App\Models\Movie::count(),
                    'route'   => route('admin.movies.index'),
                    'add_route' => route('admin.movies.create'),
                ],
                [
                    'title'   => 'Quản lý Rạp',
                    'desc'    => 'Quản lý rạp chiếu, phòng chiếu và sơ đồ ghế ngồi.',
                    'icon'    => 'fa-building',
                    'color'   => 'sky',
                    'count_label' => 'Rạp đang hoạt động',
                    'count'   => \App\Models\Cinema::count(),
                    'route'   => route('admin.cinemas.index'),
                    'add_route' => route('admin.cinemas.create'),
                ],
                [
                    'title'   => 'Suất Chiếu',
                    'desc'    => 'Lập lịch và quản lý các suất chiếu theo ngày, phòng.',
                    'icon'    => 'fa-calendar-days',
                    'color'   => 'violet',
                    'count_label' => 'Suất chiếu đã tạo',
                    'count'   => \App\Models\Showtime::count(),
                    'route'   => route('admin.showtimes.index'),
                    'add_route' => route('admin.showtimes.create'),
                ],
                [
                    'title'   => 'Quản lý Vé',
                    'desc'    => 'Theo dõi đặt vé, trạng thái thanh toán và lịch sử.',
                    'icon'    => 'fa-ticket',
                    'color'   => 'amber',
                    'count_label' => 'Vé đã đặt',
                    'count'   => \App\Models\Ticket::count(),
                    'route'   => route('admin.tickets.index'),
                    'add_route' => null,
                ],
                [
                    'title'   => 'Người Dùng',
                    'desc'    => 'Xem và quản lý tài khoản, phân quyền người dùng.',
                    'icon'    => 'fa-users',
                    'color'   => 'emerald',
                    'count_label' => 'Tài khoản đã đăng ký',
                    'count'   => \App\Models\User::count(),
                    'route'   => route('admin.users.index'),
                    'add_route' => null,
                ],
                [
                    'title'   => 'Bài Viết',
                    'desc'    => 'Theo dõi bài viết tin tức, ưu đãi và thông báo.',
                    'icon'    => 'fa-newspaper',
                    'color'   => 'pink',
                    'count_label' => 'Bài viết đã đăng',
                    'count'   => \App\Models\Post::count(),
                    'route'   => route('admin.posts.index'),
                    'add_route' => null,
                ],
            ];

            $colorMap = [
                'red'    => ['bg' => 'bg-red-500/10',    'text' => 'text-red-400',    'border' => 'border-red-500/30',    'badge' => 'bg-red-600',    'hover' => 'hover:border-red-500/40'],
                'sky'    => ['bg' => 'bg-sky-500/10',    'text' => 'text-sky-400',    'border' => 'border-sky-500/30',    'badge' => 'bg-sky-600',    'hover' => 'hover:border-sky-500/40'],
                'violet' => ['bg' => 'bg-violet-500/10', 'text' => 'text-violet-400', 'border' => 'border-violet-500/30', 'badge' => 'bg-violet-600', 'hover' => 'hover:border-violet-500/40'],
                'amber'  => ['bg' => 'bg-amber-500/10',  'text' => 'text-amber-400',  'border' => 'border-amber-500/30',  'badge' => 'bg-amber-600',  'hover' => 'hover:border-amber-500/40'],
                'emerald'=> ['bg' => 'bg-emerald-500/10','text' => 'text-emerald-400','border' => 'border-emerald-500/30','badge' => 'bg-emerald-600','hover' => 'hover:border-emerald-500/40'],
                'pink'   => ['bg' => 'bg-pink-500/10',   'text' => 'text-pink-400',   'border' => 'border-pink-500/30',   'badge' => 'bg-pink-600',   'hover' => 'hover:border-pink-500/40'],
            ];
        @endphp

        <div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
            {{-- Header --}}
            <div class="flex flex-col gap-1 md:flex-row md:items-end md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-300 mb-3">
                        <i class="fa-solid fa-layer-group"></i>
                        Trung Tâm Quản Lý
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-[rgb(255,255,255)] transition hover:text-gray-300">
                            <i class="fa-solid fa-chevron-left text-3xl md:text-4xl"></i>
                        </a>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white md:text-4xl">Tổng Quan Quản Lý</h2>
                    </div>
                    <p class="mt-2 text-gray-400">Chọn module bên dưới để quản lý từng phần của hệ thống CineBook.</p>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <i class="fa-solid fa-circle-dot text-emerald-400 animate-pulse"></i>
                    Hệ thống đang hoạt động bình thường
                </div>
            </div>

            {{-- Module Cards --}}
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($modules as $mod)
                    @php $c = $colorMap[$mod['color']]; @endphp
                    <div class="group flex flex-col rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10 transition duration-200 hover:bg-gray-900 {{ $c['hover'] }}">
                        {{-- Icon + Count --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $c['bg'] }} {{ $c['text'] }} text-xl shadow-inner">
                                <i class="fa-solid {{ $mod['icon'] }}"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-extrabold text-white">{{ number_format($mod['count']) }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $mod['count_label'] }}</div>
                            </div>
                        </div>

                        {{-- Title + Desc --}}
                        <div class="mt-5 flex-1">
                            <h3 class="text-lg font-bold text-white">{{ $mod['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-gray-400">{{ $mod['desc'] }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-6 flex items-center gap-3">
                            <a href="{{ $mod['route'] }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl {{ $c['badge'] }} px-4 py-2.5 text-sm font-bold text-white shadow transition hover:opacity-90">
                                <i class="fa-solid fa-table-list"></i>
                                Xem danh sách
                            </a>
                            @if ($mod['add_route'])
                                <a href="{{ $mod['add_route'] }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-700 bg-gray-950 px-3.5 py-2.5 text-sm font-semibold text-gray-300 transition hover:border-gray-500 hover:text-white"
                                   title="Thêm mới">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Stats Bar --}}
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/60 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-500/10 text-red-400">
                        <i class="fa-solid fa-chart-bar"></i>
                    </div>
                    <div>
                        <div class="font-bold text-white">Tổng hợp nhanh</div>
                        <div class="text-xs text-gray-500">Số liệu tổng quan toàn hệ thống</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6">
                    @foreach ($modules as $mod)
                        @php $c = $colorMap[$mod['color']]; @endphp
                        <a href="{{ $mod['route'] }}" class="group/stat flex flex-col items-center gap-2 rounded-2xl border border-gray-800 bg-gray-950/70 p-4 text-center transition hover:border-gray-600">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $c['bg'] }} {{ $c['text'] }} text-sm">
                                <i class="fa-solid {{ $mod['icon'] }}"></i>
                            </div>
                            <div class="text-xl font-extrabold text-white">{{ number_format($mod['count']) }}</div>
                            <div class="text-xs text-gray-500 leading-tight">{{ $mod['count_label'] }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @else
    
        <!-- Placeholder cho các Tab chưa làm -->
        <div class="flex min-h-[500px] items-center justify-center rounded-[2rem] border border-dashed border-gray-700 bg-gray-900/40 p-8 text-center animate-[fadeIn_0.5s_ease-in-out]">
            <div>
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-800 text-gray-400">
                    <i class="fa-solid fa-person-digging text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Đang Xây Dựng</h2>
                <p class="text-gray-400 max-w-md mx-auto">
                    Khu vực quản lý <strong>{{ $pageTitle }}</strong> đang được chuẩn bị. Vui lòng quay lại sau khi team hoàn thiện chức năng này.
                </p>
                <div class="mt-8">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                        Về lại Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<style>
.dashboard-chart-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(239, 68, 68, 0.65) rgba(17, 24, 39, 0.85);
}

.dashboard-chart-track {
    width: 100%;
}

.dashboard-chart-bar {
    max-width: 10%;
}

.dashboard-chart-scroll::-webkit-scrollbar {
    height: 10px;
}

.dashboard-chart-scroll::-webkit-scrollbar-track {
    background: rgba(17, 24, 39, 0.85);
    border-radius: 9999px;
}

.dashboard-chart-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, rgba(239, 68, 68, 0.9), rgba(251, 191, 36, 0.9));
    border-radius: 9999px;
}

.dashboard-chart-scroll::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(90deg, rgba(248, 113, 113, 0.95), rgba(252, 211, 77, 0.95));
}

@media (max-width: 1024px) {
    .dashboard-chart-track {
        width: max(100%, 120%);
    }

    .dashboard-chart-bar {
        max-width: 12%;
    }
}

@media (max-width: 768px) {
    .dashboard-chart-track {
        width: max(100%, 160%);
    }

    .dashboard-chart-bar {
        max-width: 16%;
    }
}
</style>
@endsection
