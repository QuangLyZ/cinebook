@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-white pb-20">
    <!-- Header Info -->
    <div class="bg-gray-900 border-b border-gray-800 pt-8 pb-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-6">
                <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=100&h=150&auto=format&fit=crop" class="w-16 h-24 object-cover rounded shadow" alt="Poster">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Avengers: Secret Wars</h1>
                    <p class="text-gray-400 text-sm">CineBook Landmark 81 | Rạp 3</p>
                    <p class="text-red-500 font-medium text-sm mt-1">Hôm nay - 19:45</p>
                </div>
            </div>
            
            <div class="flex gap-4 text-sm text-gray-300">
                <div class="flex items-center"><div class="w-4 h-4 rounded-sm bg-gray-600 mr-2"></div> Đã Đặt</div>
                <div class="flex items-center"><div class="w-4 h-4 rounded-sm border border-gray-500 mr-2"></div> Trống</div>
                <div class="flex items-center"><div class="w-4 h-4 rounded-sm bg-red-600 mr-2"></div> Đang Chọn</div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Seat Selection Map -->
            <div class="lg:col-span-2 bg-gray-900 p-8 rounded-2xl border border-gray-800 shadow-2xl">
                <!-- Màn Hình -->
                <div class="mb-16">
                    <div class="h-2 bg-gray-700 w-full rounded-t-full shadow-[0_10px_30px_rgba(239,68,68,0.3)] border-t border-gray-600 glow"></div>
                    <p class="text-center text-gray-500 mt-4 text-sm tracking-widest uppercase">Màn Hình Phim</p>
                </div>

                <!-- Seats -->
                <div class="flex flex-col items-center gap-3">
                    @php
                        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                    @endphp
                    @foreach($rows as $rIndex => $row)
                        <div class="flex items-center gap-4">
                            <span class="w-6 text-center text-gray-500 font-bold shrink-0">{{ $row }}</span>
                            <div class="flex gap-2">
                                @for($i = 1; $i <= 14; $i++)
                                    @php
                                        // Mock some taken seats
                                        $isTaken = rand(1, 100) > 85;
                                        // Style classes
                                        $seatClass = $isTaken 
                                            ? 'bg-gray-700 text-gray-500 cursor-not-allowed border-gray-600' 
                                            : 'border-gray-500 text-transparent hover:border-red-500 hover:text-red-500 cursor-pointer';
                                    @endphp
                                    <div class="w-8 h-8 rounded-t-lg border-2 flex items-center justify-center text-xs font-medium transition-colors {{ $seatClass }}" title="Ghế {{ $row.$i }}">
                                        {{ $i }}
                                    </div>
                                    @if($i == 4 || $i == 10)
                                        <div class="w-4"></div> <!-- Aisle -->
                                    @endif
                                @endfor
                            </div>
                            <span class="w-6 text-center text-gray-500 font-bold shrink-0">{{ $row }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Booking Summary Sidebar -->
            <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800 shadow-2xl h-fit sticky top-24">
                <h3 class="text-xl font-bold mb-6 border-b border-gray-700 pb-4">Thông Tin Đặt Vé</h3>
                
                <div class="space-y-4 mb-6 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Giá vé (Thường)</span>
                        <span class="text-white font-medium">95,000 VND</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Ghế đã chọn</span>
                        <span class="text-yellow-500 font-bold">F7, F8</span>
                    </div>
                </div>

                <div class="border-t border-gray-700 pt-4 mb-6">
                    <div class="flex justify-between items-end">
                        <span class="text-gray-400 uppercase text-xs font-bold tracking-wider">Tổng Cộng</span>
                        <span class="text-3xl font-bold text-red-500">190,000đ</span>
                    </div>
                </div>

                @guest
                <!-- Checkout Form for Guests -->
                <div class="border-t border-gray-700 pt-6 mb-6">
                    <p class="text-sm text-yellow-500 mb-4 bg-yellow-500/10 p-3 rounded border border-yellow-500/20">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Vui lòng nhập thông tin để nhận vé điện tử, hoặc <a href="/login" class="underline font-bold">đăng nhập</a> để tích điểm.
                    </p>
                    <div class="space-y-3">
                        <input type="text" placeholder="Họ và tên" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500">
                        <input type="email" placeholder="Email nhận vé" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500">
                        <input type="tel" placeholder="Số điện thoại" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500">
                    </div>
                </div>
                @endguest

                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-400 mb-3">Phương thức thanh toán</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border border-red-500 bg-red-500/10 rounded cursor-pointer transition-colors">
                            <input type="radio" name="payment" class="hidden" checked>
                            <i class="fa-solid fa-wallet text-blue-400 text-xl mr-3"></i>
                            <span class="text-sm font-bold text-white">VNPay</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-700 bg-gray-800 hover:border-gray-500 rounded cursor-pointer transition-colors">
                            <input type="radio" name="payment" class="hidden">
                            <i class="fa-brands fa-paypal text-blue-500 text-xl mr-3"></i>
                            <span class="text-sm font-bold text-white">PayPal</span>
                        </label>
                    </div>
                </div>

                <!-- Simulation Success (Using anchor to simulate redirect) -->
                <button onclick="alert('Đã giả lập gửi thông tin vé qua Email! Đặt vé thành công.')" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-red-500/30 transition-all text-lg flex justify-center items-center">
                    THANH TOÁN NGAY
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
