@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $takenSeats = collect($takenSeatNames ?? [])->map(fn ($seat) => strtoupper($seat))->all();
@endphp

<div class="min-h-screen bg-gray-950 pb-20 text-white">
    <div class="border-b border-gray-800 bg-gray-900 pt-8 pb-4">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:px-6 lg:flex-row lg:px-8">
            <div class="flex items-center gap-6">
                <img src="{{ $posterUrl }}" class="h-24 w-16 rounded object-cover shadow" alt="Poster">
                <div>
                    <h1 class="mb-1 text-2xl font-bold">{{ $showtime->movie_name ?? 'Suất chiếu đang cập nhật' }}</h1>
                    <p class="text-sm text-gray-400">
                        {{ $showtime->cinema_name ?? 'CineBook' }} | {{ $showtime->room_name ?? 'Phòng chiếu đang cập nhật' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-red-500">{{ $showDateLabel }} - {{ $showTimeLabel }}</p>
                </div>
            </div>

            <div class="flex gap-4 text-sm text-gray-300">
                <div class="flex items-center"><div class="mr-2 h-4 w-4 rounded-sm bg-gray-700"></div> Đã Đặt</div>
                <div class="flex items-center"><div class="mr-2 h-4 w-4 rounded-sm border border-gray-500"></div> Trống</div>
                <div class="flex items-center"><div class="mr-2 h-4 w-4 rounded-sm bg-red-600"></div> Đang Chọn</div>
            </div>
        </div>
    </div>

    <div class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-800 bg-gray-900 p-8 shadow-2xl lg:col-span-2">
                <div class="mb-16">
                    <div class="glow h-2 w-full rounded-t-full border-t border-gray-600 bg-gray-700 shadow-[0_10px_30px_rgba(239,68,68,0.3)]"></div>
                    <p class="mt-4 text-center text-sm uppercase tracking-widest text-gray-500">Màn Hình Phim</p>
                </div>

                <div class="flex flex-col items-center gap-3">
                    @foreach($seatMap as $rowSeats)
                        @php
                            $rowLabel = preg_replace('/\d+$/', '', $rowSeats->first()->seat_name ?? 'A') ?: 'A';
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="w-6 shrink-0 text-center font-bold text-gray-500">{{ $rowLabel }}</span>
                            <div class="flex gap-2">
                                @foreach($rowSeats as $index => $seat)
                                    @php
                                        $isTaken = in_array(strtoupper($seat->seat_name), $takenSeats, true);
                                        $seatClass = $isTaken
                                            ? 'bg-gray-700 text-gray-500 cursor-not-allowed border-gray-600'
                                            : 'border-gray-500 text-transparent hover:border-red-500 hover:text-red-500 cursor-pointer';
                                    @endphp
                                    <div
                                        data-seat="{{ strtoupper($seat->seat_name) }}"
                                        data-state="{{ $isTaken ? 'taken' : 'available' }}"
                                        class="seat-item flex h-8 w-8 items-center justify-center rounded-t-lg border-2 text-xs font-medium transition-colors {{ $seatClass }}"
                                        title="Ghế {{ strtoupper($seat->seat_name) }}"
                                    >
                                        {{ preg_replace('/^[A-Z]+/', '', strtoupper($seat->seat_name)) }}
                                    </div>
                                    @if(($index + 1) % 5 === 0 && ! $loop->last)
                                        <div class="w-4"></div>
                                    @endif
                                @endforeach
                            </div>
                            <span class="w-6 shrink-0 text-center font-bold text-gray-500">{{ $rowLabel }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sticky top-24 h-fit rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-2xl">
                <h3 class="mb-6 border-b border-gray-700 pb-4 text-xl font-bold">Thông Tin Đặt Vé</h3>

                <div class="mb-6 space-y-4 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Giá vé / ghế</span>
                        <span class="font-medium text-white">{{ number_format($seatPrice, 0, ',', '.') }} VND</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Ghế đã chọn</span>
                        <span id="selectedSeatsLabel" class="font-bold text-yellow-500">Chưa chọn</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Voucher hiện dùng</span>
                        <span id="appliedVoucherLabel" class="font-bold text-emerald-400">Chưa áp dụng</span>
                    </div>
                </div>

                <div class="mb-6 border-t border-gray-700 pt-4">
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Tổng Cộng</span>
                        <span id="bookingTotal" class="text-3xl font-bold text-red-500">0đ</span>
                    </div>
                    <p id="discountSummary" class="mt-3 hidden text-sm text-emerald-400"></p>
                </div>

                <form id="bookingForm" class="space-y-6">
                    <div class="border-t border-gray-700 pt-6">
                        <p class="mb-4 rounded border border-yellow-500/20 bg-yellow-500/10 p-3 text-sm text-yellow-500">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            @auth
                                Vé sẽ được lưu vào tài khoản của bạn ngay sau khi thanh toán.
                            @else
                                Vui lòng <a href="{{ route('login') }}" class="font-bold underline">đăng nhập</a> để thanh toán, lưu lịch sử mua vé và dùng voucher.
                            @endauth
                        </p>
                        <div class="space-y-3">
                            <input id="customerName" name="customerName" type="text" value="{{ old('customerName', $user?->fullname ?? '') }}" placeholder="Họ và tên" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-red-500 focus:outline-none" required>
                            <input id="customerEmail" name="customerEmail" type="email" value="{{ old('customerEmail', $user?->email ?? '') }}" placeholder="Email nhận vé" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-red-500 focus:outline-none" required>
                            <input id="customerPhone" name="customerPhone" type="tel" value="{{ old('customerPhone', $user?->phone ?? '') }}" placeholder="Số điện thoại" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-red-500 focus:outline-none" required>
                            <p id="formError" class="mt-1 hidden text-sm text-red-400"></p>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-3 text-sm font-medium text-gray-400">Phương thức thanh toán</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="payment-option flex cursor-pointer items-center rounded border border-red-500 bg-red-500/10 p-3 transition-colors" data-method="vnpay">
                                <input type="radio" name="payment" value="vnpay" class="hidden" checked>
                                <i class="fa-solid fa-wallet mr-3 text-xl text-blue-400"></i>
                                <span class="text-sm font-bold text-white">VNPay</span>
                            </label>
                            <label class="payment-option flex cursor-pointer items-center rounded border border-gray-700 bg-gray-800 p-3 transition-colors hover:border-gray-500" data-method="paypal">
                                <input type="radio" name="payment" value="paypal" class="hidden">
                                <i class="fa-brands fa-paypal mr-3 text-xl text-blue-500"></i>
                                <span class="text-sm font-bold text-white">PayPal</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-4">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="voucherCode" class="block text-sm font-medium text-gray-400">Mã Voucher</label>
                            <button id="openVoucherPicker" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-xs font-semibold text-white transition hover:border-red-500 hover:bg-red-600">
                                <i class="fa-solid fa-plus"></i>
                                Thêm voucher
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <input id="voucherCode" type="text" placeholder="Nhập mã voucher" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-red-500 focus:outline-none">
                            <button id="applyVoucher" type="button" class="rounded border border-gray-700 bg-gray-800 px-4 py-2 text-white transition hover:border-red-600 hover:bg-red-600">Áp dụng</button>
                        </div>
                        <p id="voucherMessage" class="mt-3 hidden text-sm text-green-400"></p>
                        <p id="voucherError" class="mt-3 hidden text-sm text-red-400"></p>
                    </div>

                    <button id="payButton" type="button" class="flex w-full items-center justify-center rounded-xl bg-red-600 py-4 text-lg font-bold text-white shadow-lg shadow-red-500/30 transition-all hover:bg-red-700">
                        XEM THANH TOÁN
                    </button>

                    <div id="paymentDetails" class="mt-4 hidden rounded-2xl border border-gray-700 bg-gray-950 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p id="paymentTitle" class="text-sm text-gray-400">Phương thức thanh toán đã chọn</p>
                                <h3 id="paymentMethodLabel" class="text-lg font-bold text-white">VNPay</h3>
                            </div>
                            <span id="countdownTimer" class="inline-flex items-center rounded-full bg-red-600 px-3 py-1 text-sm font-semibold text-white">10:00</span>
                        </div>
                        <div id="paymentContent" class="space-y-4 text-sm text-gray-300"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="voucherPickerModal" class="fixed inset-0 z-[60] hidden bg-black/70 px-4 py-10 backdrop-blur-sm">
        <div class="mx-auto max-w-3xl rounded-[2rem] border border-gray-800 bg-gray-950 shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-5">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Voucher Wallet</div>
                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Chọn voucher để áp dụng</h3>
                </div>
                <button id="closeVoucherPicker" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-gray-700 bg-gray-900 text-gray-300 transition hover:border-red-500 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="max-h-[70vh] space-y-4 overflow-y-auto px-6 py-6">
                @forelse ($availableVouchers as $voucher)
                    <article class="rounded-3xl border border-gray-800 bg-gray-900/80 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Voucher</div>
                                <h4 class="mt-2 text-2xl font-extrabold tracking-tight text-white">{{ $voucher->code }}</h4>
                                <p class="mt-3 max-w-xl text-sm leading-6 text-gray-400">{{ $voucher->description ?: 'Voucher đang sẵn sàng để áp dụng vào đơn vé của bạn.' }}</p>
                                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-400">
                                    <span class="rounded-full border border-gray-700 px-3 py-1">Hết hạn: {{ $voucher->expires_at?->format('d/m/Y H:i') ?: 'Không giới hạn' }}</span>
                                    <span class="rounded-full border border-gray-700 px-3 py-1">Còn lại: {{ is_null($voucher->usage_limit) ? 'Không giới hạn' : max($voucher->usage_limit - $voucher->used_count, 0) }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-start gap-3 md:items-end">
                                <div class="rounded-2xl bg-red-600/15 px-4 py-3 text-lg font-bold text-red-300">
                                    {{ !is_null($voucher->discount_rate) ? $voucher->discount_rate.'%' : number_format((float) $voucher->discount_value, 0, ',', '.').'đ' }}
                                </div>
                                <button type="button" class="voucher-select-btn inline-flex items-center gap-2 rounded-2xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700" data-code="{{ $voucher->code }}">
                                    <i class="fa-solid fa-check"></i>
                                    Dùng voucher này
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-12 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gray-800 text-gray-500">
                            <i class="fa-solid fa-tags text-2xl"></i>
                        </div>
                        <div class="mt-4 text-lg font-bold text-white">Hiện chưa có voucher khả dụng</div>
                        <div class="mt-2 text-sm text-gray-500">Admin cần bật voucher trước khi bạn có thể áp vào đơn vé.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentOptions = document.querySelectorAll('.payment-option');
        const paymentDetails = document.getElementById('paymentDetails');
        const paymentTitle = document.getElementById('paymentTitle');
        const paymentMethodLabel = document.getElementById('paymentMethodLabel');
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
        const appliedVoucherLabel = document.getElementById('appliedVoucherLabel');
        const discountSummary = document.getElementById('discountSummary');
        const seatElements = document.querySelectorAll('.seat-item');
        const voucherPickerModal = document.getElementById('voucherPickerModal');
        const openVoucherPicker = document.getElementById('openVoucherPicker');
        const closeVoucherPicker = document.getElementById('closeVoucherPicker');
        const voucherSelectButtons = document.querySelectorAll('.voucher-select-btn');
        const formError = document.getElementById('formError');

        const bookingConfig = {
            seatPrice: @json($seatPrice),
            checkoutUrl: @json(route('booking.checkout', $showtime->id ?? 0)),
            accountUrl: @json(route('account.index', ['tab' => 'tickets'])),
            loginUrl: @json(route('login')),
            csrf: @json(csrf_token()),
            isAuthenticated: @json(auth()->check()),
            vouchers: @json($availableVoucherPayload),
        };

        const vouchersByCode = bookingConfig.vouchers.reduce((map, voucher) => {
            map[voucher.code.toUpperCase()] = voucher;
            return map;
        }, {});

        let selectedMethod = 'vnpay';
        let countdownInterval = null;
        let selectedVoucher = null;
        let discountAmount = 0;
        let finalAmount = 0;
        let totalAmount = 0;
        let selectedSeats = [];
        let isCheckoutStarted = false;
        let isSubmitting = false;

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
                    resetCheckoutPreview();
                }
            }, 1000);
        }

        function calculateDiscount(voucher, amount) {
            if (!voucher) {
                return 0;
            }

            if (voucher.discount_rate !== null) {
                return Math.round(amount * (voucher.discount_rate / 100));
            }

            return Math.min(Number(voucher.discount_value || 0), amount);
        }

        function calculateAmounts() {
            totalAmount = selectedSeats.length * bookingConfig.seatPrice;
            discountAmount = calculateDiscount(selectedVoucher, totalAmount);
            finalAmount = Math.max(totalAmount - discountAmount, 0);
        }

        function updateBookingTotal() {
            bookingTotal.textContent = finalAmount.toLocaleString('vi-VN') + 'đ';
            if (discountAmount > 0 && selectedVoucher) {
                discountSummary.classList.remove('hidden');
                discountSummary.textContent = `Đã áp ${selectedVoucher.code}: giảm ${discountAmount.toLocaleString('vi-VN')}đ`;
                appliedVoucherLabel.textContent = selectedVoucher.code;
            } else {
                discountSummary.classList.add('hidden');
                discountSummary.textContent = '';
                appliedVoucherLabel.textContent = 'Chưa áp dụng';
            }
        }

        function updateSelectedSeatsLabel() {
            selectedSeatsLabel.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'Chưa chọn';
            calculateAmounts();
            updateBookingTotal();
        }

        function resetVoucherFeedback() {
            voucherMessage.classList.add('hidden');
            voucherError.classList.add('hidden');
            voucherMessage.textContent = '';
            voucherError.textContent = '';
        }

        function applyVoucherByCode(rawCode) {
            const code = String(rawCode || '').trim().toUpperCase();
            resetVoucherFeedback();

            if (!code) {
                selectedVoucher = null;
                calculateAmounts();
                updateBookingTotal();
                return true;
            }

            const voucher = vouchersByCode[code];
            if (!voucher) {
                selectedVoucher = null;
                calculateAmounts();
                updateBookingTotal();
                voucherError.textContent = 'Voucher không tồn tại trong danh sách hiện có của bạn.';
                voucherError.classList.remove('hidden');
                return false;
            }

            selectedVoucher = voucher;
            voucherField.value = voucher.code;
            calculateAmounts();
            updateBookingTotal();
            voucherMessage.textContent = `Đã áp dụng voucher ${voucher.code}.`;
            voucherMessage.classList.remove('hidden');
            return true;
        }

        function showFormError(message) {
            formError.textContent = message;
            formError.classList.remove('hidden');
        }

        function clearFormError() {
            formError.textContent = '';
            formError.classList.add('hidden');
        }

        function validateBookingForm() {
            if (!bookingConfig.isAuthenticated) {
                showFormError('Bạn cần đăng nhập trước khi thanh toán để hệ thống lưu vé và lịch sử voucher.');
                return false;
            }

            const name = nameField.value.trim();
            const email = emailField.value.trim();
            const phone = phoneField.value.trim().replace(/\s+/g, '');

            if (!name || !email || !phone) {
                showFormError('Vui lòng điền đầy đủ họ tên, email và số điện thoại.');
                return false;
            }

            if (name.length < 3) {
                showFormError('Họ và tên phải có ít nhất 3 ký tự.');
                return false;
            }

            if (!emailField.checkValidity()) {
                showFormError('Email không hợp lệ.');
                return false;
            }

            const phoneRegex = /^(?:0|\+84)(3|5|7|8|9)\d{8}$/;
            if (!phoneRegex.test(phone)) {
                showFormError('Số điện thoại không hợp lệ. Vui lòng nhập số di động đúng định dạng 0xxxxxxxxx hoặc +84xxxxxxxxx.');
                return false;
            }

            if (selectedSeats.length === 0) {
                showFormError('Vui lòng chọn ít nhất một ghế trước khi thanh toán.');
                return false;
            }

            clearFormError();
            return true;
        }

        function renderPaymentSection() {
            if (!validateBookingForm()) {
                return;
            }

            paymentDetails.classList.remove('hidden');
            const formattedAmount = finalAmount.toLocaleString('vi-VN') + 'đ';
            const formattedDiscount = discountAmount.toLocaleString('vi-VN') + 'đ';

            if (selectedMethod === 'vnpay') {
                paymentMethodLabel.textContent = 'VNPay';
                paymentTitle.textContent = 'Quét QR để thanh toán bằng VNPay';
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=VNPAY|${finalAmount}|${encodeURIComponent(emailField.value || 'guest@example.com')}`;
                paymentContent.innerHTML = `
                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4 text-center">
                        <div class="mx-auto mb-4 flex h-64 w-64 items-center justify-center overflow-hidden rounded-xl bg-black/10 text-gray-500">
                            <img src="${qrUrl}" alt="QR Code VNPAY" class="h-full w-full object-cover">
                        </div>
                        <p class="mb-3 font-semibold text-white">Quét mã QR để hoàn tất thanh toán</p>
                        <p class="text-gray-400">Số tiền: <span class="font-semibold text-white">${formattedAmount}</span></p>
                        ${discountAmount > 0 ? `<p class="text-gray-400">Đã giảm: <span class="font-semibold text-white">${formattedDiscount}</span></p>` : ''}
                        <p class="text-gray-400">Nội dung: <span class="font-semibold text-white">APC-TICKET</span></p>
                    </div>
                `;
                startCountdown(10 * 60);
            } else {
                paymentMethodLabel.textContent = 'PayPal';
                paymentTitle.textContent = 'Thanh toán PayPal';
                paymentContent.innerHTML = `
                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4">
                        <div class="mb-4 flex items-center justify-center gap-3">
                            <i class="fa-brands fa-paypal text-5xl text-blue-400"></i>
                        </div>
                        <p class="mb-3 text-center font-semibold text-white">Bạn sẽ được chuyển đến cổng thanh toán PayPal.</p>
                        <p class="text-gray-400">Số tiền: <span class="font-semibold text-white">${formattedAmount}</span></p>
                        ${discountAmount > 0 ? `<p class="text-gray-400">Đã giảm: <span class="font-semibold text-white">${formattedDiscount}</span></p>` : ''}
                        <p class="mt-3 text-xs text-gray-400">Khi bạn xác nhận ở bước cuối, ticket và voucher usage sẽ được lưu vào DB.</p>
                    </div>
                `;
                startCountdown(5 * 60);
            }

            isCheckoutStarted = true;
            payButton.textContent = 'XÁC NHẬN THANH TOÁN';
        }

        function resetCheckoutPreview() {
            isCheckoutStarted = false;
            paymentDetails.classList.add('hidden');
            payButton.textContent = 'XEM THANH TOÁN';
        }

        async function submitCheckout() {
            if (isSubmitting) {
                return;
            }

            isSubmitting = true;
            payButton.disabled = true;
            payButton.textContent = 'ĐANG XỬ LÝ...';
            clearFormError();

            try {
                const response = await fetch(bookingConfig.checkoutUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': bookingConfig.csrf,
                    },
                    body: JSON.stringify({
                        fullname: nameField.value.trim(),
                        email: emailField.value.trim(),
                        phone: phoneField.value.trim(),
                        payment_method: selectedMethod,
                        seat_names: selectedSeats,
                        voucher_code: selectedVoucher ? selectedVoucher.code : voucherField.value.trim(),
                    }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    const errorText = payload.message || Object.values(payload.errors || {}).flat().join(' ') || 'Thanh toán thất bại.';
                    showFormError(errorText);
                    return;
                }

                if (payload.payment_url) {
                    window.location.href = payload.payment_url;
                    return;
                }

                alert(payload.message || 'Thanh toán thành công.');
                window.location.href = payload.redirect_url || bookingConfig.accountUrl;
            } catch (error) {
                showFormError('Không thể kết nối tới hệ thống thanh toán. Vui lòng thử lại.');
            } finally {
                isSubmitting = false;
                payButton.disabled = false;
                payButton.textContent = isCheckoutStarted ? 'XÁC NHẬN THANH TOÁN' : 'XEM THANH TOÁN';
            }
        }

        paymentOptions.forEach(option => {
            option.addEventListener('click', function () {
                setActiveMethod(this.getAttribute('data-method'));
                if (isCheckoutStarted) {
                    renderPaymentSection();
                }
            });
        });

        seatElements.forEach(seat => {
            seat.addEventListener('click', function () {
                if (seat.dataset.state === 'taken') {
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
                if (isCheckoutStarted) {
                    renderPaymentSection();
                }
            });
        });

        applyVoucherBtn.addEventListener('click', function () {
            applyVoucherByCode(voucherField.value);
            if (isCheckoutStarted) {
                renderPaymentSection();
            }
        });

        openVoucherPicker.addEventListener('click', function () {
            if (!bookingConfig.isAuthenticated) {
                showFormError('Đăng nhập để hệ thống mở danh sách voucher dành cho bạn.');
                return;
            }
            voucherPickerModal.classList.remove('hidden');
        });

        closeVoucherPicker.addEventListener('click', function () {
            voucherPickerModal.classList.add('hidden');
        });

        voucherPickerModal.addEventListener('click', function (event) {
            if (event.target === voucherPickerModal) {
                voucherPickerModal.classList.add('hidden');
            }
        });

        voucherSelectButtons.forEach(button => {
            button.addEventListener('click', function () {
                applyVoucherByCode(button.dataset.code);
                voucherPickerModal.classList.add('hidden');
                if (isCheckoutStarted) {
                    renderPaymentSection();
                }
            });
        });

        payButton.addEventListener('click', function () {
            if (!isCheckoutStarted) {
                applyVoucherByCode(voucherField.value);
                renderPaymentSection();
                return;
            }

            if (!validateBookingForm()) {
                return;
            }

            submitCheckout();
        });

        setActiveMethod(selectedMethod);
        updateSelectedSeatsLabel();
    });
</script>
@endsection
