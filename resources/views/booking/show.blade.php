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
                                    <div data-seat="{{ $row.$i }}" data-state="{{ $isTaken ? 'taken' : 'available' }}" class="seat-item w-8 h-8 rounded-t-lg border-2 flex items-center justify-center text-xs font-medium transition-colors {{ $seatClass }}" title="Ghế {{ $row.$i }}">
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
                        <span id="selectedSeatsLabel" class="text-yellow-500 font-bold">Chưa chọn</span>
                    </div>
                </div>

                <div class="border-t border-gray-700 pt-4 mb-6">
                    <div class="flex justify-between items-end">
                        <span class="text-gray-400 uppercase text-xs font-bold tracking-wider">Tổng Cộng</span>
                        <span id="bookingTotal" class="text-3xl font-bold text-red-500">190,000đ</span>
                    </div>
                </div>

                <form id="bookingForm" class="space-y-6">
                    <div class="border-t border-gray-700 pt-6">
                        <p class="text-sm text-yellow-500 mb-4 bg-yellow-500/10 p-3 rounded border border-yellow-500/20">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> Vui lòng nhập thông tin để nhận vé điện tử, hoặc <a href="/login" class="underline font-bold">đăng nhập</a> để tích điểm.
                        </p>
                        <div class="space-y-3">
                            <input id="customerName" name="customerName" type="text" placeholder="Họ và tên" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500" required>
                            <input id="customerEmail" name="customerEmail" type="email" placeholder="Email nhận vé" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500" required>
                            <input id="customerPhone" name="customerPhone" type="tel" placeholder="Số điện thoại" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500" required>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-400 mb-3">Phương thức thanh toán</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="payment-option flex items-center p-3 border border-red-500 bg-red-500/10 rounded cursor-pointer transition-colors" data-method="vnpay">
                                <input type="radio" name="payment" value="vnpay" class="hidden" checked>
                                <i class="fa-solid fa-wallet text-blue-400 text-xl mr-3"></i>
                                <span class="text-sm font-bold text-white">VNPay</span>
                            </label>
                            <label class="payment-option flex items-center p-3 border border-gray-700 bg-gray-800 hover:border-gray-500 rounded cursor-pointer transition-colors" data-method="paypal">
                                <input type="radio" name="payment" value="paypal" class="hidden">
                                <i class="fa-brands fa-paypal text-blue-500 text-xl mr-3"></i>
                                <span class="text-sm font-bold text-white">PayPal</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-4">
                        <label for="voucherCode" class="block text-sm font-medium text-gray-400 mb-2">Mã Voucher</label>
                        <div class="flex gap-3 items-center">
                            <input id="voucherCode" type="text" placeholder="Nhập mã voucher" class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-red-500" value="giamgia">
                            <button id="applyVoucher" type="button" class="px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded hover:bg-red-600 hover:border-red-600 transition">Áp dụng</button>
                        </div>
                        <p id="voucherMessage" class="mt-3 text-sm text-green-400 hidden">Voucher đã áp dụng: giảm 10%.</p>
                        <p id="voucherError" class="mt-3 text-sm text-red-400 hidden">Mã voucher không hợp lệ.</p>
                    </div>

                    <button id="payButton" type="button" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-red-500/30 transition-all text-lg flex justify-center items-center">
                        THANH TOÁN NGAY
                    </button>

                    <div id="paymentDetails" class="hidden mt-4 p-4 border border-gray-700 rounded-2xl bg-gray-950">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p id="paymentTitle" class="text-sm text-gray-400">Phương thức thanh toán đã chọn</p>
                                <h3 id="paymentMethodLabel" class="text-lg font-bold text-white">VNPay</h3>
                            </div>
                            <span id="countdownTimer" class="inline-flex items-center rounded-full bg-red-600 px-3 py-1 text-sm font-semibold text-white">10:00</span>
                        </div>
                        <div id="paymentContent" class="space-y-4 text-sm text-gray-300">
                            <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4 text-center">
                                <div class="mx-auto mb-4 w-64 h-64 rounded-xl overflow-hidden bg-black/10 flex items-center justify-center text-gray-500">
                                    <img id="paymentQrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=VNPAY-190000" alt="QR Code VNPAY" class="w-full h-full object-cover">
                                </div>
                                <p class="mb-3 text-white font-semibold">Quét mã QR để chuyển khoản</p>
                                <p class="text-gray-400">Hãy hoàn tất thanh toán trong thời gian đang đếm ngược.</p>
                            </div>
                        </div>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const paymentOptions = document.querySelectorAll('.payment-option');
                        const paymentDetails = document.getElementById('paymentDetails');
                        const paymentTitle = document.getElementById('paymentTitle');
                        const paymentMethodLabel = document.getElementById('paymentMethodLabel');
                        const paymentQrImage = document.getElementById('paymentQrImage');
                        const paymentContent = document.getElementById('paymentContent');
                        const countdownTimer = document.getElementById('countdownTimer');
                        const payButton = document.getElementById('payButton');
                        const nameField = document.getElementById('customerName');
                        const emailField = document.getElementById('customerEmail');
                        const phoneField = document.getElementById('customerPhone');
                        const voucherField = document.getElementById('voucherCode');
                        const applyVoucherBtn = document.getElementById('applyVoucher');
                        const voucherMessage = document.getElementById('voucherMessage');
                        const voucherError = document.getElementById('voucherError');
                        const bookingTotal = document.getElementById('bookingTotal');
                        const selectedSeatsLabel = document.getElementById('selectedSeatsLabel');
                        const seatElements = document.querySelectorAll('.seat-item');

                        const seatPrice = 95000;
                        let selectedMethod = 'vnpay';
                        let countdownInterval = null;
                        let discountActive = false;
                        let discountAmount = 0;
                        let finalAmount = 0;
                        let totalAmount = 0;
                        let selectedSeats = [];
                        let holdTimer = null;
                        let isCheckoutStarted = false;

                        function setActiveMethod(method) {
                            selectedMethod = method;
                            paymentOptions.forEach(option => {
                                const optionMethod = option.getAttribute('data-method');
                                const radio = option.querySelector('input[type="radio"]');
                                if (optionMethod === method) {
                                    option.classList.add('border-red-500', 'bg-red-500/10');
                                    option.classList.remove('border-gray-700', 'bg-gray-800');
                                    radio.checked = true;
                                } else {
                                    option.classList.remove('border-red-500', 'bg-red-500/10');
                                    option.classList.add('border-gray-700', 'bg-gray-800');
                                    radio.checked = false;
                                }
                            });
                        }

                        function formatTime(seconds) {
                            const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
                            const remainingSeconds = (seconds % 60).toString().padStart(2, '0');
                            return `${minutes}:${remainingSeconds}`;
                        }

                        function startCountdown(duration) {
                            if (countdownInterval) {
                                clearInterval(countdownInterval);
                            }
                            let timeLeft = duration;
                            countdownTimer.textContent = formatTime(timeLeft);

                            countdownInterval = setInterval(function () {
                                timeLeft -= 1;
                                countdownTimer.textContent = formatTime(timeLeft);
                                if (timeLeft <= 0) {
                                    clearInterval(countdownInterval);
                                    countdownTimer.textContent = 'Hết giờ';
                                }
                            }, 1000);
                        }

                        function calculateAmounts() {
                            totalAmount = selectedSeats.length * seatPrice;
                            discountAmount = discountActive ? Math.round(totalAmount * 0.1) : 0;
                            finalAmount = discountActive ? Math.round(totalAmount - discountAmount) : totalAmount;
                        }

                        function updateSelectedSeatsLabel() {
                            selectedSeatsLabel.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'Chưa chọn';
                            calculateAmounts();
                            updateBookingTotal();
                        }

                        function clearSeatSelection() {
                            selectedSeats = [];
                            seatElements.forEach(seat => {
                                if (seat.dataset.state === 'selected') {
                                    seat.dataset.state = 'available';
                                    seat.classList.remove('bg-red-600', 'text-white', 'border-red-500');
                                }
                            });
                            discountActive = false;
                            calculateAmounts();
                            voucherMessage.classList.add('hidden');
                            voucherError.classList.add('hidden');
                            updateSelectedSeatsLabel();
                        }

                        function releaseHeldSeats() {
                            isCheckoutStarted = false;
                            if (holdTimer) {
                                clearTimeout(holdTimer);
                                holdTimer = null;
                            }
                            seatElements.forEach(seat => {
                                if (seat.dataset.state === 'held') {
                                    seat.dataset.state = 'available';
                                    seat.classList.remove('bg-red-600', 'text-white', 'border-red-500');
                                    seat.classList.add('border-gray-500');
                                }
                            });
                            clearSeatSelection();
                            paymentDetails.classList.add('hidden');
                            alert('Thời gian thanh toán đã hết, ghế đã được giải phóng. Vui lòng chọn lại ghế nếu cần.');
                        }

                        function startHoldTimer(duration) {
                            if (holdTimer) {
                                clearTimeout(holdTimer);
                            }
                            holdTimer = setTimeout(releaseHeldSeats, duration * 1000);
                        }

                        function renderPaymentSection() {
                            if (selectedSeats.length === 0) {
                                alert('Vui lòng chọn ghế trước khi thanh toán.');
                                return;
                            }

                            paymentDetails.classList.remove('hidden');
                            const formattedAmount = finalAmount.toLocaleString('vi-VN') + 'đ';
                            if (selectedMethod === 'vnpay') {
                                paymentMethodLabel.textContent = 'VNPay';
                                paymentTitle.textContent = 'Quét QR để thanh toán bằng VNPay';
                                paymentQrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=VNPAY|${finalAmount}|${encodeURIComponent(emailField.value || 'guest@example.com')}`;
                                paymentContent.innerHTML = `
                                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4 text-center">
                                        <div class="mx-auto mb-4 w-64 h-64 rounded-xl overflow-hidden bg-black/10 flex items-center justify-center text-gray-500">
                                            <img src="${paymentQrImage.src}" alt="QR Code VNPAY" class="w-full h-full object-cover">
                                        </div>
                                        <p class="mb-3 text-white font-semibold">Quét mã QR để hoàn tất thanh toán</p>
                                        <p class="text-gray-400">Số tiền: <span class="text-white font-semibold">${formattedAmount}</span></p>
                                        ${discountAmount > 0 ? `<p class="text-gray-400">Đã giảm: <span class="text-white font-semibold">${discountAmount.toLocaleString('vi-VN')}đ</span></p>` : ''}
                                        <p class="text-gray-400">Nội dung: <span class="text-white font-semibold">APC-TICKET</span></p>
                                    </div>
                                `;
                                startCountdown(10 * 60);
                                startHoldTimer(10 * 60);
                            } else {
                                paymentMethodLabel.textContent = 'PayPal';
                                paymentTitle.textContent = 'Thanh toán PayPal';
                                paymentContent.innerHTML = `
                                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4">
                                        <div class="flex items-center justify-center mb-4 gap-3">
                                            <i class="fa-brands fa-paypal text-5xl text-blue-400"></i>
                                        </div>
                                        <p class="mb-3 text-white font-semibold text-center">Bạn sẽ được chuyển đến cổng thanh toán PayPal.</p>
                                        <p class="text-gray-400">Số tiền: <span class="text-white font-semibold">${formattedAmount}</span></p>
                                        ${discountAmount > 0 ? `<p class="text-gray-400">Đã giảm: <span class="text-white font-semibold">${discountAmount.toLocaleString('vi-VN')}đ</span></p>` : ''}
                                        <p class="text-gray-400">Vui lòng hoàn tất thanh toán trong <span class="text-white font-semibold">05:00</span>.</p>
                                        <p class="text-gray-400 text-xs mt-3">Giá vé đã được cập nhật theo voucher.</p>
                                    </div>
                                `;
                                startCountdown(5 * 60);
                                startHoldTimer(5 * 60);
                            }

                            isCheckoutStarted = true;
                            selectedSeats.forEach(seatId => {
                                const seat = document.querySelector(`[data-seat="${seatId}"]`);
                                if (seat) {
                                    seat.dataset.state = 'held';
                                    seat.classList.add('bg-red-600', 'text-white', 'border-red-500');
                                    seat.classList.remove('border-gray-500');
                                }
                            });
                        }

                        paymentOptions.forEach(option => {
                            option.addEventListener('click', function () {
                                const method = this.getAttribute('data-method');
                                setActiveMethod(method);
                            });
                        });

                        seatElements.forEach(seat => {
                            seat.addEventListener('click', function () {
                                if (seat.dataset.state === 'taken' || seat.dataset.state === 'held') {
                                    return;
                                }

                                if (isCheckoutStarted) {
                                    alert('Đang giữ ghế trong thanh toán. Bạn không thể thay đổi ghế lúc này.');
                                    return;
                                }

                                const seatId = seat.dataset.seat;
                                const seatIndex = selectedSeats.indexOf(seatId);
                                if (seatIndex === -1) {
                                    selectedSeats.push(seatId);
                                    seat.dataset.state = 'selected';
                                    seat.classList.add('bg-red-600', 'text-white', 'border-red-500');
                                    seat.classList.remove('border-gray-500');
                                } else {
                                    selectedSeats.splice(seatIndex, 1);
                                    seat.dataset.state = 'available';
                                    seat.classList.remove('bg-red-600', 'text-white', 'border-red-500');
                                    seat.classList.add('border-gray-500');
                                }
                                updateSelectedSeatsLabel();
                            });
                        });

                        function updateBookingTotal() {
                            bookingTotal.textContent = finalAmount.toLocaleString('vi-VN') + 'đ';
                        }

                        function applyVoucher() {
                            const code = voucherField.value.trim().toLowerCase();
                            if (code === 'giamgia' && selectedSeats.length > 0) {
                                discountActive = true;
                                calculateAmounts();
                                voucherMessage.classList.remove('hidden');
                                voucherError.classList.add('hidden');
                                updateBookingTotal();
                                return true;
                            }

                            discountActive = false;
                            calculateAmounts();
                            voucherMessage.classList.add('hidden');
                            voucherError.classList.remove('hidden');
                            updateBookingTotal();
                            return false;
                        }

                        applyVoucherBtn.addEventListener('click', function () {
                            applyVoucher();
                        });

                        payButton.addEventListener('click', function () {
                            const name = nameField.value.trim();
                            const email = emailField.value.trim();
                            const phone = phoneField.value.trim();

                            if (!name || !email || !phone) {
                                alert('Vui lòng điền đầy đủ họ tên, email và số điện thoại để tiếp tục.');
                                return;
                            }

                            applyVoucher();
                            renderPaymentSection();
                        });

                        setActiveMethod(selectedMethod);
                        updateBookingTotal();
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
