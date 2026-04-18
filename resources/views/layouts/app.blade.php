<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CineBook - Đặt vé xem phim nhanh chóng')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 cho Popup -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                        <a href="{{ route('cinemas.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Danh Sách Rạp</a>
                        <a href="/feedback" class="text-gray-300 hover:text-white px-3 py-2 rounded-md font-medium transition-colors">Góp Ý & Hỗ Trợ</a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search Form with Suggestions -->
                    <div class="hidden md:block relative z-50">
                        <form action="{{ route('movies.index') }}" method="GET" class="flex relative items-center">
                            <input type="text" name="q" id="globalSearchInput" placeholder="Tìm kiếm phim, rạp..." autocomplete="off" class="bg-gray-800 border border-gray-700 rounded-full pl-4 pr-10 py-1.5 text-sm text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 w-48 transition-all duration-300 focus:w-64">
                            <button type="submit" class="absolute right-3 text-gray-400 hover:text-white transition-colors">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </form>
                        <!-- Suggestions Dropdown -->
                        <div id="searchSuggestions" class="absolute top-full right-0 mt-2 w-64 bg-gray-800 border border-gray-700 rounded-lg shadow-xl overflow-hidden hidden"></div>
                    </div>
                    <a href="{{ route('movies.index') }}" class="md:hidden text-gray-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-search"></i>
                    </a>

                    @guest
                        <a href="/login" class="text-gray-300 hover:text-white px-3 py-2 font-medium transition-colors">Đăng Nhập</a>
                        <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium shadow transition-colors">Đăng Ký</a>
                    @else
                        <div class="relative flex items-center gap-3">
                            <a href="{{ route('account.index') }}" class="hidden rounded-lg border border-gray-700 px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:border-gray-600 hover:text-white md:inline-flex">
                                Tài khoản
                            </a>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('globalSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let searchTimeout;

    if (searchInput && searchSuggestions) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            
            if (q.length < 1) {
                searchSuggestions.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/movies/suggestions?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            searchSuggestions.innerHTML = data.map(movie => `
                                <a href="/movies?q=${encodeURIComponent(movie.name)}" class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-b border-gray-700/50 last:border-0 truncate" title="${movie.name}">
                                    <i class="fa-solid fa-film mr-2 text-gray-500"></i> ${movie.name}
                                </a>
                            `).join('');
                            searchSuggestions.classList.remove('hidden');
                        } else {
                            searchSuggestions.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500 italic text-center">Không tìm thấy phim phù hợp</div>`;
                            searchSuggestions.classList.remove('hidden');
                        }
                    })
                    .catch(err => console.error("Search error:", err));
            }, 300);
        });

        // Hide when clicked outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.classList.add('hidden');
            }
        });

        // Reopen when focused
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 1 && searchSuggestions.innerHTML.trim() !== '') {
                searchSuggestions.classList.remove('hidden');
            }
        });
    }
});
</script>

<!-- Toast Notification cho các thông báo Thành công & Lỗi thông thường (Đăng nhập, thêm phim...) -->
@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#fff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: `{!! session('success') !!}`
        });
        @endif

        @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: `{!! session('error') !!}`
        });
        @endif
    });
</script>
@endif

@if(session('payment_success') || session('payment_error'))
<!-- Custom Payment Popup -->
<div id="paymentPopup" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-[#1a1d24] rounded-2xl shadow-2xl border border-gray-700/50 w-full max-w-sm p-8 text-center relative transform scale-100 transition-transform duration-300">
        <!-- Close Button -->
        <button onclick="document.getElementById('paymentPopup').remove()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>

        @if(session('payment_success'))
            <!-- Success Icon -->
            <div class="mx-auto w-16 h-16 bg-[#10b981] rounded-full flex items-center justify-center mb-6 shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                <i class="fa-solid fa-check text-white text-3xl"></i>
            </div>
            
            <p class="text-[#10b981] text-xs font-bold tracking-[0.2em] uppercase mb-2">Payment Success</p>
            <h3 class="text-white text-2xl font-bold mb-3">Thanh toán thành công</h3>
            <p class="text-gray-400 text-sm mb-8 leading-relaxed px-4">{!! session('payment_success') !!}</p>
            
            <a href="{{ route('account.index', ['tab' => 'tickets']) }}" onclick="document.getElementById('paymentPopup').remove()" class="inline-block bg-[#10b981] hover:bg-[#059669] text-white font-medium px-8 py-2.5 rounded-full transition-colors shadow-[0_4px_14px_0_rgba(16,185,129,0.39)]">
                Xem vé của tôi
            </a>
        @else
            <!-- Error Icon -->
            <div class="mx-auto w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-6 shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                <i class="fa-solid fa-xmark text-white text-3xl"></i>
            </div>
            
            <p class="text-red-500 text-xs font-bold tracking-[0.2em] uppercase mb-2">Payment Failed</p>
            <h3 class="text-white text-2xl font-bold mb-3">Thanh toán thất bại</h3>
            <p class="text-gray-400 text-sm mb-8 leading-relaxed px-4">{!! session('payment_error') !!}</p>
            
            <button onclick="document.getElementById('paymentPopup').remove()" class="bg-red-500 hover:bg-red-600 text-white font-medium px-8 py-2.5 rounded-full transition-colors shadow-[0_4px_14px_0_rgba(239,68,68,0.39)]">
                Thử lại
            </button>
        @endif
    </div>
</div>

<script>
    // Click outside to close
    document.getElementById('paymentPopup').addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
</script>
@endif

</body>
</html>
