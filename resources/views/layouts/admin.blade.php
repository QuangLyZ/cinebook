<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'CineBook') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-950 text-gray-100">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="hidden border-r border-gray-800 bg-gray-950 lg:flex lg:flex-col">
            <div class="border-b border-gray-800 px-7 py-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-900/30">
                        <i class="fa-solid fa-film text-lg"></i>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.28em] text-gray-500">Admin Panel</div>
                        <div class="text-2xl font-extrabold tracking-tight text-red-500">Cine<span class="text-white">Book</span></div>
                    </div>
                </a>
            </div>

            <div class="flex-1 px-5 py-6">
                <div class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.28em] text-gray-500">Điều hướng</div>
                <nav class="space-y-2">
                    @php
                        $adminNav = [
                            'dashboard' => ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-chart-line'],
                            'management' => ['label' => 'Quản lý', 'route' => 'admin.management', 'icon' => 'fa-layer-group'],
                            'posts' => ['label' => 'Bài viết', 'route' => 'admin.posts.index', 'icon' => 'fa-newspaper'],
                            'actions' => ['label' => 'Action', 'route' => 'admin.actions', 'icon' => 'fa-bolt'],
                            'feedback' => ['label' => 'Ý kiến phản hồi', 'route' => 'admin.feedback', 'icon' => 'fa-comments'],
                        ];
                    @endphp

                    @foreach ($adminNav as $key => $item)
                        @php $isActive = ($activeTab ?? 'dashboard') === $key; @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="{{ $isActive ? 'bg-red-600 text-white shadow-lg shadow-red-950/30' : 'text-gray-300 hover:bg-gray-900 hover:text-white' }} flex items-center gap-3 rounded-2xl border {{ $isActive ? 'border-red-500/60' : 'border-transparent' }} px-4 py-3 text-sm font-semibold transition"
                        >
                            <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="mt-8 rounded-3xl border border-gray-800 bg-gray-900/70 p-4">
                    <div class="text-xs uppercase tracking-[0.24em] text-red-400">Workspace</div>
                    <p class="mt-3 text-sm leading-6 text-gray-400">
                        Layout admin dùng cùng tinh thần điện ảnh với phần user, để team chỉ việc mở rộng từng module.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-800 px-6 py-5 text-sm text-gray-400">
                <div class="font-semibold text-white">{{ Auth::user()->name ?? 'Admin User' }}</div>
                <div class="mt-1">Quản trị hệ thống</div>
            </div>
        </aside>

        <div class="relative flex min-h-screen flex-col bg-gradient-to-b from-gray-950 via-gray-900 to-black">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(220,38,38,0.18),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(239,68,68,0.12),transparent_30%)]"></div>

            <header class="sticky top-0 z-30 border-b border-gray-800 bg-gray-950/85 backdrop-blur-md">
                <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-900/30 lg:hidden">
                            <i class="fa-solid fa-film"></i>
                        </a>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">CineBook Admin</div>
                            <h1 class="text-xl font-extrabold tracking-tight text-white">@yield('page-title', 'Dashboard')</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="hidden items-center gap-2 rounded-2xl border border-gray-800 bg-gray-900/80 px-4 py-3 text-sm font-semibold text-gray-300 transition hover:border-gray-700 hover:text-white md:inline-flex">
                            <i class="fa-solid fa-house"></i>
                            <span>Trang chủ</span>
                        </a>
                        <label class="hidden min-w-[260px] items-center gap-3 rounded-2xl border border-gray-800 bg-gray-900/80 px-4 py-3 md:flex">
                            <i class="fa-solid fa-magnifying-glass text-gray-500"></i>
                            <input type="text" placeholder="Tìm phim, rạp, bài viết..." class="w-full border-0 bg-transparent p-0 text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none focus:ring-0">
                        </label>
                        <button class="flex h-11 w-11 items-center justify-center rounded-2xl border border-gray-800 bg-gray-900/80 text-gray-400 transition hover:border-gray-700 hover:text-white">
                            <i class="fa-regular fa-bell"></i>
                        </button>
                        <div class="flex items-center gap-3 rounded-2xl border border-gray-800 bg-gray-900/80 px-3 py-2 text-white">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 font-bold text-white shadow-inner">
                                {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1, 'UTF-8'), 'UTF-8') }}
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-sm font-semibold">{{ Auth::user()->name ?? 'Admin User' }}</div>
                                <div class="text-xs text-gray-400">System Owner</div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-red-950/30 transition hover:bg-red-700">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="relative z-10 flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
