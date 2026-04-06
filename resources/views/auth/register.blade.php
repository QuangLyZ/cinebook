@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('register_bg.jpg') }}');">
    <!-- Overlay tối để làm nổi bật form và dịu mắt -->
    <div class="absolute inset-0 bg-black/60 z-0"></div>

    <!-- Container Form Glassmorphism (Blur Mica) -->
    <div class="w-full max-w-md space-y-8 p-10 rounded-[2rem] shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] border border-white/10 relative z-10 block backdrop-blur-xl bg-gray-900/40">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-white tracking-widest font-sans mb-2" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                Đăng Ký Tài Khoản
            </h2>
            <p class="text-center text-sm text-gray-300">
                Đã có tài khoản?
                <a href="/login" class="font-bold text-red-500 hover:text-red-400 transition-colors drop-shadow-md">
                    Đăng nhập ngay
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-200 mb-1">Họ và Tên</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="Nguyễn Văn A">
                    @error('name') <span class="text-xs text-red-400 mt-1 block drop-shadow-md">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="example@email.com">
                    @error('email') <span class="text-xs text-red-400 mt-1 block drop-shadow-md">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-1">Mật khẩu</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="••••••••">
                    @error('password') <span class="text-xs text-red-400 mt-1 block drop-shadow-md">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-200 mb-1">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-red-500/50 text-lg font-bold rounded-xl text-white bg-red-600/80 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-transparent transition-all shadow-[0_0_15px_rgba(220,38,38,0.5)] hover:shadow-[0_0_25px_rgba(220,38,38,0.8)] backdrop-blur-sm tracking-wider">
                    ĐĂNG KÝ
                </button>
            </div>
            
            <p class="text-xs text-center text-gray-300 mt-4">
                Bằng việc đăng ký, bạn đã đồng ý với <a href="#" class="text-red-400 hover:text-white transition-colors">Điều khoản</a> và <a href="#" class="text-red-400 hover:text-white transition-colors">Chính sách bảo mật</a> của chúng tôi.
            </p>
        </form>
    </div>
</div>
@endsection