<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CineBook') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100 flex flex-col min-h-screen relative">
    
    <!-- Navbar -->
    <nav class="bg-gray-900/80 backdrop-blur-md border-b border-gray-700 fixed w-full z-50 top-0 start-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="/" class="flex items-center text-red-500 font-bold text-2xl tracking-tighter">
                        <i class="fa-solid fa-film mr-2 text-red-600"></i>
                        Cine<span class="text-white">Book</span>
                    </a>
                    
                    <div class="hidden md:flex items-baseline space-x-4 ml-10">
                        <a href="/" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Trang Chủ</a>
                        <a href="/movies" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Phim & Lịch Chiếu</a>
                        <a href="/theaters" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Danh Sách Rạp</a>
                        <a href="/feedback" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Góp Ý & Hỗ Trợ</a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search Icon (Mock) -->
                    <button class="text-gray-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    @guest
                        <a href="/login" class="text-gray-300 hover:text-white px-3 py-2 font-medium transition-colors">Đăng Nhập</a>
                        <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium shadow transition-colors">Đăng Ký</a>
                    @else
                        <div class="relative flex items-center gap-3">
                            <span class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                            <!-- For simplicity in UI mock, logout form is omitted -->
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Đăng Xuất">
                                    <i class="fa-solid fa-sign-out-alt"></i>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 pt-10 pb-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="/" class="flex items-center text-red-500 font-bold text-2xl tracking-tighter mb-4">
                        <i class="fa-solid fa-film mr-2 text-red-600"></i> Cine<span class="text-white">Book</span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed mb-4">
                        Hệ thống đặt vé xem phim trực tuyến hàng đầu, mang đến trải nghiệm tuyệt vời và tiện lợi nhất cho người dùng.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">CineBook</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-red-500 transition-colors">Về chúng tôi</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Tin tức</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">Hỗ Trợ</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-red-500 transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-red-500 transition-colors">Giải đáp câu hỏi (FAQs)</a></li>
                        <li><a href="/feedback" class="hover:text-red-500 transition-colors">Góp ý</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4 uppercase text-sm tracking-wider">Tải Ứng Dụng</h3>
                    <div class="space-y-3">
                        <a href="#" class="flex flex-col items-start bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg px-3 py-2 transition-colors">
                             <div class="text-xs text-gray-400">Download on the</div>
                             <div class="text-sm font-semibold text-white"><i class="fa-brands fa-apple mr-2"></i>App Store</div>
                        </a>
                        <a href="#" class="flex flex-col items-start bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg px-3 py-2 transition-colors">
                             <div class="text-xs text-gray-400">GET IT ON</div>
                             <div class="text-sm font-semibold text-white"><i class="fa-brands fa-google-play mr-2"></i>Google Play</div>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} CineBook. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
