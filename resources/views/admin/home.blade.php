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
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-lg shadow-red-900/20 hover:bg-red-700 transition">
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
            $cards = [
                ['label' => 'Người dùng hoạt động', 'value' => '1,284', 'delta' => '+12%', 'tone' => 'bg-sky-500/15 text-sky-300', 'icon' => 'fa-users'],
                ['label' => 'Đặt vé hôm nay', 'value' => '356', 'delta' => '+8%', 'tone' => 'bg-red-500/15 text-red-300', 'icon' => 'fa-ticket'],
                ['label' => 'Bài viết xuất bản', 'value' => '42', 'delta' => '+5 mới', 'tone' => 'bg-emerald-500/15 text-emerald-300', 'icon' => 'fa-newspaper'],
                ['label' => 'Hành động chờ duyệt', 'value' => '17', 'delta' => 'Cần xử lý', 'tone' => 'bg-amber-500/15 text-amber-300', 'icon' => 'fa-bolt'],
            ];

            $quickActions = [
                ['label' => 'Thêm phim mới', 'desc' => 'Tạo nội dung phim và nối lịch chiếu sau.', 'icon' => 'fa-film'],
                ['label' => 'Tạo bài viết', 'desc' => 'Soạn tin tức, ưu đãi hoặc thông báo chiến dịch.', 'icon' => 'fa-pen-nib'],
                ['label' => 'Quản lý rạp', 'desc' => 'Cập nhật rạp, phòng chiếu, ghế và sơ đồ.', 'icon' => 'fa-building'],
                ['label' => 'Ý kiến phản hồi', 'desc' => 'Xem và phản hồi các góp ý từ khách hàng.', 'icon' => 'fa-comments'],
            ];

            $activity = [
                ['title' => 'Lịch chiếu cuối tuần đã được đồng bộ', 'time' => '10 phút trước', 'tag' => 'Scheduler'],
                ['title' => 'Bài viết ưu đãi tháng 4 được đẩy lên bản nháp', 'time' => '32 phút trước', 'tag' => 'Content'],
                ['title' => 'Rạp CineBook Landmark cập nhật 2 phòng chiếu mới', 'time' => '1 giờ trước', 'tag' => 'Cinema'],
                ['title' => 'Hệ thống vừa ghi nhận 87 giao dịch thành công', 'time' => '2 giờ trước', 'tag' => 'Orders'],
            ];
        @endphp

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px] animate-[fadeIn_0.5s_ease-in-out]">
            <div class="space-y-6">
                <div class="overflow-hidden rounded-[2rem] border border-gray-800 bg-gradient-to-br from-gray-900 via-gray-900 to-black shadow-2xl shadow-black/30">
                    <div class="grid gap-8 px-6 py-8 md:grid-cols-[1.2fr_0.8fr] md:px-8 md:py-10">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-300">
                                <i class="fa-solid fa-clapperboard"></i>
                                Admin Workspace
                            </div>
                            <h2 class="mt-5 max-w-2xl text-3xl font-extrabold tracking-tight text-white md:text-4xl">
                                Khu điều hành CineBook cho đội admin, cùng hệ màu và cảm giác với giao diện user.   
                            </h2>
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-400 md:text-base">
                                Khung này ưu tiên tính phân tầng rõ ràng: sidebar cho điều hướng chính, topbar cho thao tác nhanh, nội dung giữa để team bạn tiếp tục gắn dashboard, bảng quản lý và form CRUD.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('admin.management') }}" class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                                    <i class="fa-solid fa-arrow-right"></i>
                                    Đi tới khu quản lý
                                </a>
                                <a href="{{ route('admin.feedback') }}" class="inline-flex items-center gap-2 rounded-2xl border border-gray-700 bg-gray-900 px-5 py-3 text-sm font-semibold text-gray-200 transition hover:border-red-500/40 hover:text-white">
                                    <i class="fa-solid fa-comments"></i>
                                    Xem phản hồi
                                </a>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-1">
                            @foreach ($cards as $card)
                                <div class="rounded-3xl border border-gray-800 bg-gray-900/70 p-5 backdrop-blur transition hover:bg-gray-900">   
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="text-sm text-gray-400">{{ $card['label'] }}</div>
                                            <div class="mt-3 text-3xl font-extrabold text-white">{{ $card['value'] }}</div>
                                        </div>
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                            <i class="fa-solid {{ $card['icon'] }}"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm font-semibold text-gray-300">{{ $card['delta'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Quick Actions</div>
                                <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Lối tắt thao tác</h3>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-300">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                        </div>
                        <div class="mt-6 grid gap-4">
                            @foreach ($quickActions as $item)
                                <div class="rounded-3xl border border-gray-800 bg-gray-950/80 p-4 transition hover:border-red-500/30 hover:bg-gray-900 cursor-pointer">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-900/20">
                                            <i class="fa-solid {{ $item['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ $item['label'] }}</div>
                                            <div class="mt-1 text-sm leading-6 text-gray-400">{{ $item['desc'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Module Status</div>
                                <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Tiến độ chia tab</h3>
                            </div>
                            <div class="rounded-2xl bg-red-500/10 px-3 py-2 text-sm font-semibold text-red-300">Ready for team</div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($adminTabs as $key => $label)
                                <div class="rounded-3xl border {{ $activeTab === $key ? 'border-red-500/40 bg-red-500/10' : 'border-gray-800 bg-gray-950/70' }} p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-bold text-white">{{ $label }}</div>
                                            <div class="mt-1 text-sm text-gray-400">
                                                {{ $activeTab === $key ? 'Tab đang được focus để thiết kế và test giao diện.' : 'Sẵn khung điều hướng, chờ team gắn chức năng chi tiết.' }}
                                            </div>
                                        </div>
                                        <span class="rounded-full {{ $activeTab === $key ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300' }} px-3 py-1 text-xs font-bold uppercase tracking-[0.2em]">
                                            {{ $activeTab === $key ? 'Active' : 'Queued' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-6 shadow-lg shadow-black/10">    
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Recent Activity</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Hoạt động gần đây</h3>       
                    <div class="mt-6 space-y-4">
                        @foreach ($activity as $item)
                            <div class="relative rounded-3xl border border-gray-800 bg-gray-950/70 p-4 pl-6 transition hover:border-gray-700">       
                                <div class="absolute left-0 top-6 h-10 w-1 rounded-r-full bg-red-600"></div>        
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-white">{{ $item['title'] }}</div>
                                        <div class="mt-2 text-sm text-gray-500">{{ $item['time'] }}</div>
                                    </div>
                                    <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-gray-300">
                                        {{ $item['tag'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
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
