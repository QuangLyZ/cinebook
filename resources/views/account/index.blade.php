@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-950 pb-20 text-white">
    <section class="border-b border-gray-800 bg-[radial-gradient(circle_at_top_right,rgba(220,38,38,0.16),transparent_28%),linear-gradient(180deg,#111827_0%,#030712_100%)]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.24em] text-red-300">
                        <i class="fa-solid fa-id-card"></i>
                        CineBook Account
                    </div>
                    <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-white md:text-5xl">Tài khoản của tôi</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-400 md:text-base">
                        Theo dõi vé đã mua, voucher đang có thể dùng và lịch sử ưu đãi ngay trong một màn hình. Mục này được làm để user tự kiểm tra trạng thái giao dịch mà không cần hỏi admin.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Vé đã mua</div>
                        <div class="mt-3 text-3xl font-extrabold text-white">{{ $accountStats['ticket_count'] }}</div>
                        <div class="mt-2 text-sm text-gray-400">Lịch sử booking của bạn trên hệ thống.</div>
                    </div>
                    <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Tổng chi</div>
                        <div class="mt-3 text-3xl font-extrabold text-white">{{ number_format((float) $accountStats['ticket_spent'], 0, ',', '.') }}đ</div>
                        <div class="mt-2 text-sm text-gray-400">Tính theo tổng tiền cuối cùng sau giảm giá.</div>
                    </div>
                    <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Voucher khả dụng</div>
                        <div class="mt-3 text-3xl font-extrabold text-white">{{ $accountStats['available_vouchers'] }}</div>
                        <div class="mt-2 text-sm text-gray-400">Các voucher đang mở và chưa hết điều kiện sử dụng.</div>
                    </div>
                    <div class="rounded-[1.75rem] border border-gray-800 bg-gray-900/80 p-5 shadow-lg shadow-black/10">
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Đã dùng voucher</div>
                        <div class="mt-3 text-3xl font-extrabold text-white">{{ $accountStats['used_vouchers'] }}</div>
                        <div class="mt-2 text-sm text-gray-400">Số lần ưu đãi đã được áp vào vé của bạn.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        @php
            $tabs = [
                'tickets' => ['label' => 'Vé của tôi', 'icon' => 'fa-ticket'],
                'vouchers' => ['label' => 'Voucher khả dụng', 'icon' => 'fa-tags'],
                'usage' => ['label' => 'Lịch sử dùng voucher', 'icon' => 'fa-clock-rotate-left'],
            ];
            $activeTab = in_array($accountTab, array_keys($tabs), true) ? $accountTab : 'tickets';
        @endphp

        <div class="mb-6 flex flex-wrap gap-3">
            @foreach ($tabs as $key => $tab)
                <a
                    href="{{ route('account.index', ['tab' => $key]) }}"
                    class="{{ $activeTab === $key ? 'border-red-500/50 bg-red-600 text-white shadow-lg shadow-red-950/25' : 'border-gray-800 bg-gray-900/80 text-gray-300 hover:border-gray-700 hover:text-white' }} inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-sm font-semibold transition"
                >
                    <i class="fa-solid {{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>

        @if ($activeTab === 'tickets')
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 shadow-lg shadow-black/10">
                <div class="border-b border-gray-800 px-6 py-5">
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Purchase History</div>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Lịch sử mua vé</h2>
                </div>

                <div class="divide-y divide-gray-800">
                    @forelse ($tickets as $ticket)
                        <article class="grid gap-6 px-6 py-6 lg:grid-cols-[minmax(0,1fr)_220px]">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-xl font-bold text-white">{{ $ticket->movie_name ?: 'Phim đang cập nhật' }}</h3>
                                    <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-gray-300">#{{ $ticket->id }}</span>
                                    @if ($ticket->voucher_code)
                                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-300">
                                            Voucher {{ $ticket->voucher_code }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-3 grid gap-4 text-sm text-gray-400 md:grid-cols-2">
                                    <div class="rounded-3xl border border-gray-800 bg-gray-950/70 p-4">
                                        <div class="font-semibold text-white">Suất chiếu</div>
                                        <div class="mt-2">{{ $ticket->cinema_name ?: 'CineBook' }} {{ $ticket->room_name ? '| '.$ticket->room_name : '' }}</div>
                                        <div class="mt-1 text-gray-500">{{ $ticket->start_time?->format('d/m/Y H:i') ?: 'Đang cập nhật lịch chiếu' }}</div>
                                        <div class="mt-2 text-sm font-bold text-yellow-500">Ghế: {{ $ticket->seat_names ?: 'Không có' }}</div>
                                    </div>
                                    <div class="rounded-3xl border border-gray-800 bg-gray-950/70 p-4">
                                        <div class="font-semibold text-white">Thông tin nhận vé</div>
                                        <div class="mt-2">{{ $ticket->fullname ?: auth()->user()->name }}</div>
                                        <div class="mt-1 text-gray-500">{{ $ticket->email ?: auth()->user()->email }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-gray-800 bg-gray-950/70 p-5">
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">Thanh toán</div>
                                <div class="mt-4 text-sm text-gray-400">Ngày đặt</div>
                                <div class="mt-1 font-semibold text-white">{{ $ticket->booking_date?->format('d/m/Y H:i') ?: '--' }}</div>
                                <div class="mt-4 text-sm text-gray-400">Giảm giá</div>
                                <div class="mt-1 font-semibold text-emerald-300">{{ number_format((float) $ticket->discount_amount, 0, ',', '.') }}đ</div>
                                <div class="mt-4 text-sm text-gray-400">Tổng thanh toán</div>
                                <div class="mt-1 text-2xl font-extrabold text-red-400">{{ number_format((float) $ticket->final_price, 0, ',', '.') }}đ</div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-800 text-gray-500">
                                <i class="fa-solid fa-ticket text-3xl"></i>
                            </div>
                            <h3 class="mt-5 text-2xl font-bold text-white">Bạn chưa có giao dịch nào</h3>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-500">Khi bạn đặt vé thành công, lịch sử mua sẽ hiện ở đây cùng thông tin voucher đã áp dụng.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @elseif ($activeTab === 'vouchers')
            <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                @forelse ($availableVouchers as $voucher)
                    <article class="overflow-hidden rounded-[2rem] border border-gray-800 bg-gray-900/80 shadow-lg shadow-black/10">
                        <div class="border-b border-gray-800 bg-[linear-gradient(135deg,rgba(127,29,29,0.95),rgba(17,24,39,0.95))] px-6 py-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-red-200/80">Voucher</div>
                                    <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-white">{{ $voucher->code }}</h3>
                                </div>
                                <div class="rounded-2xl bg-white/10 px-4 py-2 text-lg font-bold text-white">
                                    {{ !is_null($voucher->discount_rate) ? $voucher->discount_rate.'%' : number_format((float) $voucher->discount_value, 0, ',', '.').'đ' }}
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 px-6 py-5 text-sm text-gray-300">
                            <p class="leading-6 text-gray-400">{{ $voucher->description ?: 'Voucher đang sẵn sàng sử dụng trên hệ thống CineBook.' }}</p>
                            <div class="grid gap-3 text-sm md:grid-cols-2">
                                <div class="rounded-2xl border border-gray-800 bg-gray-950/60 p-4">
                                    <div class="text-xs uppercase tracking-[0.2em] text-gray-500">Hiệu lực</div>
                                    <div class="mt-2 font-semibold text-white">{{ $voucher->starts_at?->format('d/m/Y H:i') ?: 'Ngay bây giờ' }}</div>
                                </div>
                                <div class="rounded-2xl border border-gray-800 bg-gray-950/60 p-4">
                                    <div class="text-xs uppercase tracking-[0.2em] text-gray-500">Hết hạn</div>
                                    <div class="mt-2 font-semibold text-white">{{ $voucher->expires_at?->format('d/m/Y H:i') ?: 'Không giới hạn' }}</div>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-gray-800 bg-gray-950/60 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-gray-400">Số lượt còn lại</span>
                                    <span class="font-semibold text-white">
                                        {{ is_null($voucher->usage_limit) ? 'Không giới hạn' : max($voucher->usage_limit - $voucher->used_count, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="lg:col-span-2 xl:col-span-3 rounded-[2rem] border border-dashed border-gray-700 bg-gray-900/40 px-6 py-16 text-center">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-800 text-gray-500">
                            <i class="fa-solid fa-tags text-3xl"></i>
                        </div>
                        <h3 class="mt-5 text-2xl font-bold text-white">Hiện chưa có voucher khả dụng</h3>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-500">Khi admin bật voucher và voucher còn trong thời hạn sử dụng, danh sách ưu đãi sẽ xuất hiện ở đây.</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="rounded-[2rem] border border-gray-800 bg-gray-900/80 shadow-lg shadow-black/10">
                <div class="border-b border-gray-800 px-6 py-5">
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-gray-500">Usage Timeline</div>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-white">Lịch sử sử dụng voucher</h2>
                </div>

                <div class="divide-y divide-gray-800">
                    @forelse ($voucherUsages as $usage)
                        <article class="flex flex-col gap-5 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-300">{{ $usage->voucher_code }}</div>
                                    <div class="text-sm text-gray-500">Ticket #{{ $usage->ticket_id }}</div>
                                </div>
                                <h3 class="mt-3 text-xl font-bold text-white">{{ $usage->movie_name ?: 'Phim đang cập nhật' }}</h3>
                                <div class="mt-2 text-sm text-gray-400">{{ $usage->used_at?->format('d/m/Y H:i') ?: '--' }}</div>
                            </div>
                            <div class="rounded-3xl border border-gray-800 bg-gray-950/70 px-5 py-4 text-right">
                                <div class="text-sm text-gray-400">Số tiền đã giảm</div>
                                <div class="mt-1 text-2xl font-extrabold text-emerald-300">{{ number_format((float) $usage->discount_amount, 0, ',', '.') }}đ</div>
                            </div>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-800 text-gray-500">
                                <i class="fa-solid fa-clock-rotate-left text-3xl"></i>
                            </div>
                            <h3 class="mt-5 text-2xl font-bold text-white">Bạn chưa dùng voucher nào</h3>
                            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-500">Mỗi lần voucher được áp vào đơn vé thành công, lịch sử ưu đãi sẽ được lưu tại đây.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </section>

    <div
        id="paymentSuccessModal"
        class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 px-4 backdrop-blur-sm"
        aria-hidden="true"
    >
        <div class="relative w-full max-w-md overflow-hidden rounded-[2rem] border border-emerald-400/20 bg-gray-900 shadow-2xl shadow-emerald-950/20">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-400"></div>
            <button
                id="closePaymentSuccessModal"
                type="button"
                class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-gray-300 transition hover:border-emerald-400/40 hover:text-white"
                aria-label="Đóng thông báo thanh toán"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="px-8 py-10 text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-500/15 ring-8 ring-emerald-500/10">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-check text-3xl"></i>
                    </div>
                </div>

                <div class="mt-6 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Payment Success</div>
                <h3 id="paymentSuccessTitle" class="mt-3 text-3xl font-extrabold tracking-tight text-white">Thanh toán thành công</h3>
                <p id="paymentSuccessMessage" class="mt-4 text-sm leading-7 text-gray-300">
                    Vé của bạn đã được thanh toán và được gửi qua email.
                </p>

                <button
                    id="confirmPaymentSuccessModal"
                    type="button"
                    class="mt-8 inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-400"
                >
                    Xem vé của tôi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('paymentSuccessModal');
        const closeButton = document.getElementById('closePaymentSuccessModal');
        const confirmButton = document.getElementById('confirmPaymentSuccessModal');
        const titleElement = document.getElementById('paymentSuccessTitle');
        const messageElement = document.getElementById('paymentSuccessMessage');
        const storageKey = 'cinebook_payment_success_popup';
        const sessionPopup = @json(session('success') === 'Thanh toán thành công!' ? [
            'title' => 'Thanh toán thành công',
            'message' => 'Vé của bạn đã được thanh toán và được gửi qua email.',
        ] : null);

        function hideModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
        }

        function showModal(data) {
            titleElement.textContent = data.title || 'Thanh toán thành công';
            messageElement.textContent = data.message || 'Vé của bạn đã được thanh toán và được gửi qua email.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        }

        function readPopupFromStorage() {
            try {
                const rawValue = sessionStorage.getItem(storageKey);
                if (!rawValue) {
                    return null;
                }

                sessionStorage.removeItem(storageKey);
                return JSON.parse(rawValue);
            } catch (error) {
                sessionStorage.removeItem(storageKey);
                return null;
            }
        }

        const popupData = sessionPopup || readPopupFromStorage();

        if (popupData) {
            showModal(popupData);
        }

        closeButton.addEventListener('click', hideModal);
        confirmButton.addEventListener('click', hideModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                hideModal();
            }
        });
    });
</script>
@endsection
