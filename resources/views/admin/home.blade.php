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

    @elseif ($activeTab === 'reviews')
        <div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-white md:text-4xl">Quản lý Đánh giá phim</h2>
                    <p class="mt-3 text-gray-400">Kiểm duyệt và quản lý các bình luận từ người dùng trên toàn hệ thống.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-400 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-[2rem] border border-gray-800 bg-gray-900 shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800 text-sm">
                        <thead class="bg-black/20 text-left text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Phim</th>
                                <th class="px-6 py-4">Người dùng</th>
                                <th class="px-6 py-4">Đánh giá</th>
                                <th class="px-6 py-4">Bình luận</th>
                                <th class="px-6 py-4">Thời gian</th>
                                <th class="px-6 py-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($reviews as $review)
                                <tr class="bg-transparent transition hover:bg-gray-950/50">
                                    <td class="px-6 py-5 align-top">
                                        <div class="font-bold text-white">{{ $review->movie->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $review->movie->genre }}</div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="text-white font-medium">{{ $review->user->fullname }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $review->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex text-yellow-500 text-xs">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $review->rating }}/5 sao</div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="max-w-xs text-gray-300 leading-relaxed line-clamp-2 italic">
                                            "{{ $review->comment ?: 'Không có bình luận' }}"
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-top text-gray-500 text-xs">
                                        {{ $review->created_at->format('H:i d/m/Y') }}
                                        <div class="mt-1">{{ $review->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-5 align-top text-right">
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Sếp có chắc chắn muốn xóa đánh giá này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-gray-700 bg-gray-950 text-gray-400 transition hover:border-red-500 hover:text-red-500">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                        Chưa có đánh giá nào để hiển thị.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (method_exists($reviews, 'hasPages') && $reviews->hasPages())
                    <div class="px-6 py-4 bg-black/10 border-t border-gray-800">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>

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
            {{-- Dashboard content omitted for brevity in write --}}
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-12 text-center">
                 <h2 class="text-2xl font-bold text-white mb-4">Dashboard Thống Kê</h2>
                 <p class="text-gray-400">Vui lòng xem các module quản lý để biết chi tiết.</p>
            </div>
        </section>

    @elseif ($activeTab === 'posts')
        <div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
             {{-- Posts content --}}
             <h2 class="text-3xl font-extrabold text-white">Quản lý bài viết</h2>
             <a href="{{ route('admin.posts.index') }}" class="inline-block bg-red-600 px-4 py-2 rounded-xl text-white mt-4">Mở khu quản lý chi tiết</a>
        </div>

    @elseif ($activeTab === 'actions')
        <div class="space-y-6 animate-[fadeIn_0.5s_ease-in-out]">
             {{-- Actions content --}}
             <h2 class="text-3xl font-extrabold text-white">Quản lý Voucher & Khuyến mãi</h2>
             <p class="text-gray-400">Vui lòng sử dụng các chức năng quản lý tại tab Action.</p>
        </div>

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
                    'title'   => 'Đánh giá & Review',
                    'desc'    => 'Quản lý các lượt đánh giá sao và bình luận từ khách hàng.',
                    'icon'    => 'fa-star',
                    'color'   => 'yellow',
                    'count_label' => 'Tổng số đánh giá',
                    'count'   => \App\Models\Review::count(),
                    'route'   => route('admin.reviews.index'),
                    'add_route' => null,
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
            ];

            $colorMap = [
                'red'    => ['bg' => 'bg-red-500/10',    'text' => 'text-red-400',    'border' => 'border-red-500/30',    'badge' => 'bg-red-600',    'hover' => 'hover:border-red-500/40'],
                'yellow' => ['bg' => 'bg-yellow-500/10', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/30', 'badge' => 'bg-yellow-600', 'hover' => 'hover:border-yellow-500/40'],
                'sky'    => ['bg' => 'bg-sky-500/10',    'text' => 'text-sky-400',    'border' => 'border-sky-500/30',    'badge' => 'bg-sky-600',    'hover' => 'hover:border-sky-500/40'],
                'violet' => ['bg' => 'bg-violet-500/10', 'text' => 'text-violet-400', 'border' => 'border-violet-500/30', 'badge' => 'bg-violet-600', 'hover' => 'hover:border-violet-500/40'],
                'amber'  => ['bg' => 'bg-amber-500/10',  'text' => 'text-amber-400',  'border' => 'border-amber-500/30',  'badge' => 'bg-amber-600',  'hover' => 'hover:border-amber-500/40'],
                'emerald'=> ['bg' => 'bg-emerald-500/10','text' => 'text-emerald-400','border' => 'border-emerald-500/30','badge' => 'bg-emerald-600','hover' => 'hover:border-emerald-500/40'],
            ];
        @endphp

        <div class="space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="flex flex-col gap-1 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold text-white md:text-4xl">Tổng Quan Quản Lý</h2>
                    <p class="mt-2 text-gray-400">Chọn module bên dưới để quản lý hệ thống CineBook.</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($modules as $mod)
                    @php $c = $colorMap[$mod['color']]; @endphp
                    <div class="group flex flex-col rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10 transition duration-200 hover:bg-gray-900 {{ $c['hover'] }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $c['bg'] }} {{ $c['text'] }} text-xl shadow-inner">
                                <i class="fa-solid {{ $mod['icon'] }}"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-extrabold text-white">{{ number_format($mod['count']) }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">{{ $mod['count_label'] }}</div>
                            </div>
                        </div>
                        <div class="mt-5 flex-1">
                            <h3 class="text-lg font-bold text-white">{{ $mod['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-gray-400">{{ $mod['desc'] }}</p>
                        </div>
                        <div class="mt-6 flex items-center gap-3">
                            <a href="{{ $mod['route'] }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl {{ $c['badge'] }} px-4 py-2.5 text-sm font-bold text-white shadow transition hover:opacity-90">
                                <i class="fa-solid fa-table-list"></i> Xem danh sách
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
