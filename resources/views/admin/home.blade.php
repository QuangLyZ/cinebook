@extends('layouts.admin')

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('content')
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

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
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
                            <div class="rounded-3xl border border-gray-800 bg-gray-900/70 p-5 backdrop-blur">
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
                            <div class="rounded-3xl border border-gray-800 bg-gray-950/80 p-4 transition hover:border-red-500/30 hover:bg-gray-900">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-600 text-white">
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
                        <div class="relative rounded-3xl border border-gray-800 bg-gray-950/70 p-4 pl-6">
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

            <div class="overflow-hidden rounded-[2rem] border border-red-500/20 bg-gradient-to-br from-red-950/40 via-gray-900 to-black p-6 shadow-2xl shadow-black/20">
                <div class="text-xs font-semibold uppercase tracking-[0.24em] text-red-300">Current Focus</div>
                <h3 class="mt-3 text-2xl font-extrabold tracking-tight text-white">{{ $pageTitle }}</h3>
                <p class="mt-3 text-sm leading-7 text-gray-400">
                    Đây là vùng nội dung chính cho tab <span class="font-bold text-white">{{ $pageTitle }}</span>. Team bạn có thể thay phần này bằng bảng, biểu đồ, form hoặc CRUD mà không cần sửa lại khung điều hướng.
                </p>
                <div class="mt-6 rounded-3xl border border-gray-800 bg-gray-900/70 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white">Placeholder module</div>
                            <div class="mt-1 text-sm text-gray-400">Khung trống để các bạn phát triển từng chức năng con.</div>
                        </div>
                        <i class="fa-solid fa-diagram-project text-2xl text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
