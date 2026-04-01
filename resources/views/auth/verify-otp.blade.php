@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex justify-center">
    <div class="bg-gray-800 p-8 rounded-2xl border border-gray-700 shadow-xl w-full max-w-md">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white mb-2">Xác Thực OTP</h2>
            <p class="text-gray-400 text-sm">Vui lòng nhập mã 6 số chúng tôi vừa gửi đến email của bạn.</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600 text-white p-4 rounded-lg mb-6 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6 text-sm text-center font-bold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-300 mb-1">Mã OTP Của Bạn</label>
                <input type="text" id="otp" name="otp" required maxlength="6" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white text-center text-2xl tracking-[0.5em] focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500 transition-colors" placeholder="------">
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex justify-center items-center text-lg">
                <i class="fa-solid fa-check-circle mr-2"></i> Xác Thực Ngay
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-400 text-sm">Chưa nhận được mã? <a href="{{ route('otp.send') }}" class="text-blue-500 hover:text-blue-400 font-medium ml-1">Gửi lại OTP</a></p>
        </div>
    </div>
</div>
@endsection
