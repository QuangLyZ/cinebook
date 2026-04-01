@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-10 rounded-2xl shadow-2xl border border-gray-700 relative z-10">
        <div>
            <div class="flex justify-center mb-4">
                <i class="fa-solid fa-key text-5xl text-red-500"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-white">
                Khôi Phục Mật Khẩu
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Nhập email của bạn để nhận liên kết đặt lại mật khẩu.
            </p>
        </div>
        <form class="mt-8 space-y-6" action="#" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email đã đăng ký</label>
                <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-3 py-3 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="example@email.com">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-900 transition-colors">
                    GỬI LIÊN KẾT
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="/login" class="font-medium text-gray-400 hover:text-white transition-colors text-sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
