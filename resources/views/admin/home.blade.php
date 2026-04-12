@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
    @if ($activeTab === 'settings')
        <div class="max-w-4xl space-y-8 animate-[fadeIn_0.5s_ease-in-out]">
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold tracking-tight text-white md:text-4xl">Cài đặt Hệ thống</h2>
                <p class="mt-3 text-gray-400">Thiết lập các ngưỡng cảnh báo vận hành và thông báo qua email cho quản trị viên.</p>
            </div>

            <!-- Block 1: Ngưỡng cảnh báo -->
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-8 shadow-lg shadow-black/10 transition hover:border-gray-700">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Ngưỡng cảnh báo hoạt động</h3>
                        <p class="text-sm text-gray-400">Hệ thống sẽ báo động khi các chỉ số vượt mức an toàn.</p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Tỉ lệ payment error -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Tỉ lệ Payment Error/Tháng (%)</label>
                        <div class="relative">
                            <input type="number" name="payment_error_threshold" value="{{ $settings['payment_error_threshold'] ?? '15' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Cảnh báo nếu tỷ lệ lỗi thanh toán vượt ngưỡng.</p>
                    </div>

                    <!-- Tỷ lệ fill rạp -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Ngưỡng Tỷ lệ Fill rạp/Tháng (%)</label>
                        <div class="relative">
                            <input type="number" name="min_fill_rate" value="{{ $settings['min_fill_rate'] ?? '20' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Cảnh báo nếu tỷ lệ lấp đầy ghế dưới mức này.</p>
                    </div>

                    <!-- Doanh thu tối thiểu -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Cảnh báo Doanh thu (VND/Tháng)</label>
                        <input type="number" name="min_monthly_revenue" value="{{ $settings['min_monthly_revenue'] ?? '150000000' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <p class="mt-2 text-xs text-gray-500">Báo động khi doanh thu tháng sụt giảm.</p>
                    </div>

                    <!-- Cảnh báo số suất chiếu -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Cảnh báo Suất chiếu trống/Tháng</label>
                        <input type="number" name="min_empty_showtimes" value="{{ $settings['min_empty_showtimes'] ?? '5' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <p class="mt-2 text-xs text-gray-500">Số lượng ghế bán ra dưới mức quy định.</p>
                    </div>

                    <!-- Phim sắp hết hạn -->
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Cảnh báo Phim (Ngày/Tháng)</label>
                        <div class="relative">
                            <input type="number" name="movie_expiry_notice" value="{{ $settings['movie_expiry_notice'] ?? '7' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Ngày</div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Báo trước số ngày khi một phim chuẩn bị ngừng chiếu.</p>
                    </div>
                </div>
            </div>

            <!-- Block 2: Email Notifications -->
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 p-8 shadow-lg shadow-black/10 transition hover:border-gray-700">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-500/10 text-sky-400">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Cấu hình Thông báo Email</h3>
                        <p class="text-sm text-gray-400">Quản lý cách hệ thống báo cáo tình trạng vận hành cho bạn.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Toggle Switch -->
                    <label class="flex cursor-pointer items-center gap-4 w-max">
                        <div class="relative">
                            <input type="checkbox" name="email_notification_active" value="true" class="peer sr-only" {{ ($settings['email_notification_active'] ?? 'false') == 'true' ? 'checked' : '' }}>
                            <div class="h-7 w-12 rounded-full bg-gray-700 transition peer-checked:bg-red-600"></div>
                            <div class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white transition peer-checked:translate-x-5 shadow-sm"></div>
                        </div>
                        <span class="text-sm font-bold text-white">Bật tự động gửi Email cảnh báo</span>
                    </label>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Email nhận cảnh báo</label>
                        <input type="email" name="admin_email" value="{{ $settings['admin_email'] ?? 'admin@cinebook.com' }}" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white placeholder-gray-500 transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <p class="mt-2 text-xs text-gray-500">Phân tách nhiều email bằng dấu phẩy (,)</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-300">Tần suất thông báo</label>
                        <select name="notification_frequency" class="w-full rounded-xl border border-gray-700 bg-black/50 px-4 py-3 text-white transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 appearance-none">
                            <option value="realtime" {{ ($settings['notification_frequency'] ?? '') == 'realtime' ? 'selected' : '' }}>Tức thì (Ngay khi có lỗi hoặc vượt ngưỡng)</option>
                            <option value="daily" {{ ($settings['notification_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Tổng hợp báo cáo cuối ngày</option>
                            <option value="weekly" {{ ($settings['notification_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Tổng hợp báo cáo hàng tuần</option>
                            <option value="monthly" {{ ($settings['notification_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Tổng hợp báo cáo hàng tháng</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 border-t border-gray-800/60 pt-6">
                <button type="button" class="rounded-xl border border-gray-700 bg-transparent px-6 py-3 text-sm font-bold text-gray-300 transition hover:bg-gray-800 hover:text-white">
                    Hủy thay đổi
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Lưu cấu hình
                </button>
            </div>
        </form>

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
                ['label' => 'Cấu hình hệ thống', 'desc' => 'Điều chỉnh thông số vận hành và tài khoản.', 'icon' => 'fa-gear'],
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
                                <a href="{{ route('admin.settings') }}" class="inline-flex items-center gap-2 rounded-2xl border border-gray-700 bg-gray-900 px-5 py-3 text-sm font-semibold text-gray-200 transition hover:border-red-500/40 hover:text-white">
                                    <i class="fa-solid fa-sliders"></i>
                                    Xem cài đặt
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
                    <h2 class="text-3xl font-extrabold tracking-tight text-white md:text-4xl">Tổng Quan Quản Lý</h2>
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



