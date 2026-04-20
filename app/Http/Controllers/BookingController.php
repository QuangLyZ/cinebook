<?php

namespace App\Http\Controllers;

use App\Mail\BookingTicketMail;
use App\Models\User;
use App\Notifications\BookingStatusNotification;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    private const SEAT_PRICE = 95000;

    public function show(Request $request, int $showtimeId): View
    {
        $showtime = null;
        $seats = collect();
        $takenSeatNames = [];
        $availableVouchers = collect();

        try {
            $showtime = DB::table('showtimes')
                ->join('movies', 'movies.id', '=', 'showtimes.movie_id')
                ->join('rooms', 'rooms.id', '=', 'showtimes.room_id')
                ->join('cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
                ->where('showtimes.id', $showtimeId)
                ->select([
                    'showtimes.id',
                    'showtimes.start_time',
                    'showtimes.room_id',
                    'movies.name as movie_name',
                    'movies.poster',
                    'movies.age_limit',
                    'rooms.name as room_name',
                    'cinemas.name as cinema_name',
                ])
                ->first();

            abort_if(!$showtime, 404);

            $seats = DB::table('seats')
                ->where('room_id', $showtime->room_id)
                ->orderBy('seat_name')
                ->select(['id', 'seat_name', 'seat_type'])
                ->get();

            $takenSeatNames = DB::table('ticket_details')
                ->join('tickets', 'tickets.id', '=', 'ticket_details.ticket_id')
                ->join('seats', 'seats.id', '=', 'ticket_details.seat_id')
                ->where('tickets.showtime_id', $showtimeId)
                ->where(function($query) {
                    $query->where('tickets.status', 'paid')
                          ->orWhereNull('tickets.status')
                          ->orWhere(function($q) {
                              $q->where('tickets.status', 'pending')
                                ->where('tickets.created_at', '>=', now()->subMinutes(10));
                          });
                })
                ->pluck('seats.seat_name')
                ->all();

            if ($request->user()) {
                $availableVouchers = $this->loadAvailableVouchers();
            }
        } catch (QueryException) {
            // Keep the booking UI renderable even if local schema is incomplete.
        }

        $startTime = $showtime?->start_time ? Carbon::parse($showtime->start_time) : null;
        $seatMap = $this->buildSeatMap($seats);
        $voucherPayload = $availableVouchers->map(fn($voucher) => [
            'code' => $voucher->code,
            'discount_value' => $voucher->discount_value ? (int) $voucher->discount_value : null,
            'discount_rate' => $voucher->discount_rate,
            'description' => $voucher->description,
            'expires_at' => $voucher->expires_at?->format('d/m/Y H:i'),
            'remaining_uses' => is_null($voucher->usage_limit) ? null : max($voucher->usage_limit - $voucher->used_count, 0),
        ])->values();

        return view('booking.show', [
            'showtime' => $showtime,
            'showDateLabel' => $startTime?->isToday()
                ? 'Hom nay'
                : ($startTime ? ucfirst($startTime->translatedFormat('l, d/m/Y')) : 'Lich chieu dang cap nhat'),
            'showTimeLabel' => $startTime?->format('H:i') ?? '--:--',
            'posterUrl' => filled($showtime?->poster)
                ? $showtime->poster
                : 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=100&h=150&auto=format&fit=crop',
            'seatMap' => $seatMap,
            'takenSeatNames' => $takenSeatNames,
            'seatPrice' => self::SEAT_PRICE,
            'availableVouchers' => $availableVouchers,
            'availableVoucherPayload' => $voucherPayload,
        ]);
    }

    public function checkout(Request $request, int $showtimeId): JsonResponse
    {
        if (!$request->user()) {
            $this->recordPaymentLog([
                'user_id' => null,
                'ticket_id' => null,
                'showtime_id' => $showtimeId,
                'payment_method' => (string) $request->input('payment_method', 'unknown'),
                'status' => 'failed',
                'attempted_at' => now(),
                'amount' => 0,
                'discount_amount' => 0,
                'final_amount' => 0,
                'seat_count' => count((array) $request->input('seat_names', [])),
                'voucher_code' => $request->input('voucher_code'),
                'customer_email' => $request->input('email'),
                'customer_phone' => $request->input('phone'),
                'error_message' => 'Nguoi dung chua dang nhap.',
                'metadata' => [
                    'seat_names' => (array) $request->input('seat_names', []),
                ],
            ]);

            return response()->json([
                'message' => 'Bạn cần đăng nhập để hoàn tất thanh toán và lưu lịch sử vé',
                'login_url' => route('login'),
            ], 401);
        }

        try {
            $data = $request->validate([
                'fullname' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'payment_method' => ['required', 'in:vnpay,paypal'],
                'seat_names' => ['required', 'array', 'min:1'],
                'seat_names.*' => ['required', 'string'],
                'voucher_code' => ['nullable', 'string', 'max:50'],
            ], [
                'seat_names.required' => 'Vui lòng chọn ít nhất một ghế.',
            ]);

            $result = DB::transaction(function () use ($request, $showtimeId, $data) {
                $showtime = DB::table('showtimes')->where('id', $showtimeId)->first();

                if (!$showtime) {
                    throw ValidationException::withMessages([
                        'showtime' => 'Suất chiếu không còn tồn tại.',
                    ]);
                }

                $seatNames = collect($data['seat_names'])
                    ->map(fn($seat) => strtoupper(trim($seat)))
                    ->unique()
                    ->values();

                Log::info('=== SEAT DEBUG ===', [
                    'showtime_id' => $showtimeId,
                    'room_id' => $showtime->room_id,
                    'seat_names_in' => $seatNames->all(),
                    'all_seats_in_room' => DB::table('seats')
                        ->where('room_id', $showtime->room_id)
                        ->pluck('seat_name')
                        ->all(),
                ]);

                $seats = DB::table('seats')
                    ->where('room_id', $showtime->room_id)
                    ->whereIn(DB::raw('UPPER(seat_name)'), $seatNames)
                    ->select(['id', 'seat_name'])
                    ->get();

                Log::info('=== SEAT QUERY RESULT ===', [
                    'seats_found' => $seats->count(),
                    'seats_data' => $seats->toArray(),
                ]);

                if ($seats->count() !== $seatNames->count()) {
                    throw ValidationException::withMessages([
                        'seat_names' => 'Một hoặc nhiều ghế không hợp lệ cho phòng chiếu này.',
                    ]);
                }

                $alreadyBooked = DB::table('ticket_details')
                    ->join('tickets', 'tickets.id', '=', 'ticket_details.ticket_id')
                    ->where('tickets.showtime_id', $showtimeId)
                    ->whereIn('ticket_details.seat_id', $seats->pluck('id'))
                    ->where(function($query) {
                        $query->where('tickets.status', 'paid')
                              ->orWhereNull('tickets.status')
                              ->orWhere(function($q) {
                                  $q->where('tickets.status', 'pending')
                                    ->where('tickets.created_at', '>=', now()->subMinutes(10));
                              });
                    })
                    ->lockForUpdate() // Ngăn chặn các request khác đọc/ghi vào các dòng này cho đến khi transaction xong
                    ->exists();

                if ($alreadyBooked) {
                    throw ValidationException::withMessages([
                        'seat_names' => 'Một hoặc nhiều ghế vừa được người khác đặt. Vui lòng chọn lại.',
                    ]);
                }

                $baseTotal = $seatNames->count() * self::SEAT_PRICE;
                $voucher = $this->resolveVoucher($data['voucher_code'] ?? null);
                $discountAmount = $voucher ? $this->calculateDiscount($voucher, $baseTotal) : 0;
                $finalPrice = max($baseTotal - $discountAmount, 0);
                $referenceCode = 'PAY-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
                $ticketCode = $this->generateUniqueTicketCode();

                $ticketId = DB::table('tickets')->insertGetId([
                    'user_id' => $request->user()->id,
                    'showtime_id' => $showtimeId,
                    'fullname' => $data['fullname'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'booking_date' => now(),
                    'total_price' => $baseTotal,
                    'discount_amount' => $discountAmount,
                    'final_price' => $finalPrice,
                    'voucher_code' => $voucher?->code,
                    'reference_code' => $referenceCode,
                    'ticket_code' => $ticketCode,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $seatRows = $seats->map(fn($seat) => [
                    'ticket_id' => $ticketId,
                    'seat_id' => $seat->id,
                    'price_at_booking' => self::SEAT_PRICE,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                DB::table('ticket_details')->insert($seatRows);

                if ($voucher) {
                    DB::table('voucher_usages')->insert([
                        'voucher_id' => $voucher->id,
                        'user_id' => $request->user()->id,
                        'ticket_id' => $ticketId,
                        'voucher_code' => $voucher->code,
                        'discount_amount' => $discountAmount,
                        'used_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('vouchers')
                        ->where('id', $voucher->id)
                        ->update([
                            'used_count' => DB::raw('used_count + 1'),
                            'updated_at' => now(),
                        ]);
                }

                return [
                    'ticket_id' => $ticketId,
                    'voucher_code' => $voucher?->code,
                    'discount_amount' => $discountAmount,
                    'final_price' => $finalPrice,
                    'total_price' => $baseTotal,
                    'seat_names' => $seatNames->all(),
                    'payment_method' => $data['payment_method'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'reference_code' => $referenceCode,
                    'ticket_code' => $ticketCode,
                    'fullname' => $data['fullname'],
                ];
            });

            $this->recordPaymentLog([
                'user_id' => $request->user()->id,
                'ticket_id' => $result['ticket_id'],
                'showtime_id' => $showtimeId,
                'payment_method' => $result['payment_method'],
                'status' => 'success',
                'attempted_at' => now(),
                'amount' => $result['total_price'],
                'discount_amount' => $result['discount_amount'],
                'final_amount' => $result['final_price'],
                'seat_count' => count($result['seat_names']),
                'voucher_code' => $result['voucher_code'],
                'reference_code' => $result['reference_code'],
                'customer_email' => $result['email'],
                'customer_phone' => $result['phone'],
                'error_message' => null,
                'metadata' => [
                    'seat_names' => $result['seat_names'],
                ],
            ]);

            if ($data['payment_method'] === 'vnpay') {
                $request->user()->notify(new BookingStatusNotification(
                    'pending',
                    'Đặt vé thành công',
                    'Đặt chỗ đã được ghi nhận. Vui lòng hoàn tất thanh toán VNPay để xác nhận vé.',
                    route('account.index', ['tab' => 'tickets'])
                ));

                $paymentUrl = $this->createVnpayUrl($result['final_price'], $result['ticket_id']);

                return response()->json(['payment_url' => $paymentUrl]);
            }

            $emailDelivered = $this->sendTicketConfirmationEmail($result['ticket_id'], $data['payment_method']);

            $request->user()->notify(new BookingStatusNotification(
                'success',
                'Đặt vé thành công',
                $emailDelivered
                ? 'Thanh toán thành công. Vé đã được gửi tới email của bạn.'
                : 'Thanh toán thành công. Vé đã được lưu trong tài khoản của bạn.',
                route('account.index', ['tab' => 'tickets'])
            ));

            DB::table('tickets')->where('id', $result['ticket_id'])->update([
                'status' => 'paid',
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => $emailDelivered
                    ? 'Thanh toán thành công. Vé đã được gửi tới email nhận vé của bạn.'
                    : 'Thanh toán thành công. Vé đã được lưu vào tài khoản của bạn.',
                'redirect_url' => route('account.index', ['tab' => 'tickets']),
                'ticket' => $result,
                'ticket_email' => $result['email'],
                'email_sent' => $emailDelivered,
            ]);
        } catch (ValidationException $exception) {
            $request->user()?->notify(new BookingStatusNotification(
                'failed',
                'Đặt vé thất bại',
                collect($exception->errors())->flatten()->first() ?: 'Đã có lỗi xảy ra khi đặt vé.',
                route('booking.show', ['id' => $showtimeId])
            ));

            $this->recordPaymentLog([
                'user_id' => $request->user()?->id,
                'ticket_id' => null,
                'showtime_id' => $showtimeId,
                'payment_method' => (string) $request->input('payment_method', 'unknown'),
                'status' => 'failed',
                'attempted_at' => now(),
                'amount' => count((array) $request->input('seat_names', [])) * self::SEAT_PRICE,
                'discount_amount' => 0,
                'final_amount' => count((array) $request->input('seat_names', [])) * self::SEAT_PRICE,
                'seat_count' => count((array) $request->input('seat_names', [])),
                'voucher_code' => $request->input('voucher_code'),
                'customer_email' => $request->input('email'),
                'customer_phone' => $request->input('phone'),
                'error_message' => collect($exception->errors())->flatten()->first(),
                'metadata' => [
                    'errors' => $exception->errors(),
                    'seat_names' => (array) $request->input('seat_names', []),
                ],
            ]);

            throw $exception;
        } catch (\Throwable $exception) {
            $request->user()?->notify(new BookingStatusNotification(
                'failed',
                'Thanh toán thất bại',
                $exception->getMessage() ?: 'Đã có lỗi không xác định, vui lòng thử lại.',
                route('booking.show', ['id' => $showtimeId])
            ));

            $this->recordPaymentLog([
                'user_id' => $request->user()?->id,
                'ticket_id' => null,
                'showtime_id' => $showtimeId,
                'payment_method' => (string) $request->input('payment_method', 'unknown'),
                'status' => 'failed',
                'attempted_at' => now(),
                'amount' => count((array) $request->input('seat_names', [])) * self::SEAT_PRICE,
                'discount_amount' => 0,
                'final_amount' => count((array) $request->input('seat_names', [])) * self::SEAT_PRICE,
                'seat_count' => count((array) $request->input('seat_names', [])),
                'voucher_code' => $request->input('voucher_code'),
                'customer_email' => $request->input('email'),
                'customer_phone' => $request->input('phone'),
                'error_message' => $exception->getMessage(),
                'metadata' => [
                    'seat_names' => (array) $request->input('seat_names', []),
                ],
            ]);

            Log::error('Booking checkout failed unexpectedly.', [
                'message' => $exception->getMessage(),
                'showtime_id' => $showtimeId,
            ]);

            return response()->json([
                'message' => 'Thanh toán chưa hoàn tất, vui lòng thử lại.',
            ], 500);
        }
    }

    protected function loadAvailableVouchers(): Collection
    {
        return DB::table('vouchers')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->orderBy('expires_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($voucher) {
                $voucher->starts_at = $voucher->starts_at ? Carbon::parse($voucher->starts_at) : null;
                $voucher->expires_at = $voucher->expires_at ? Carbon::parse($voucher->expires_at) : null;

                return $voucher;
            });
    }

    protected function resolveVoucher(?string $voucherCode): ?object
    {
        $normalizedCode = strtoupper(trim((string) $voucherCode));

        if ($normalizedCode === '') {
            return null;
        }

        $voucher = DB::table('vouchers')
            ->where('code', $normalizedCode)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher không tồn tại hoặc đang bị tắt.',
            ]);
        }

        if ($voucher->starts_at && Carbon::parse($voucher->starts_at)->isFuture()) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher chưa đến thời gian áp dụng.',
            ]);
        }

        if ($voucher->expires_at && Carbon::parse($voucher->expires_at)->isPast()) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher đã hết hạn.',
            ]);
        }

        if (!is_null($voucher->usage_limit) && (int) $voucher->used_count >= (int) $voucher->usage_limit) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher đã hết lượt sử dụng.',
            ]);
        }

        return $voucher;
    }

    protected function calculateDiscount(object $voucher, int $baseTotal): int
    {
        if (!is_null($voucher->discount_rate)) {
            return (int) round($baseTotal * ((int) $voucher->discount_rate / 100));
        }

        return min((int) $voucher->discount_value, $baseTotal);
    }

    protected function buildSeatMap(Collection $seats): Collection
    {
        if ($seats->isEmpty()) {
            return collect(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'])->map(function ($row) {
                return collect(range(1, 14))->map(fn($number) => (object) [
                    'id' => null,
                    'seat_name' => $row . $number,
                    'seat_type' => 'standard',
                ]);
            });
        }

        return $seats
            ->groupBy(fn($seat) => preg_replace('/\d+$/', '', $seat->seat_name) ?: 'A')
            ->sortKeys()
            ->map(fn($group) => $group->sortBy(function ($seat) {
                preg_match('/(\d+)$/', $seat->seat_name, $matches);

                return (int) ($matches[1] ?? 0);
            })->values())
            ->values();
    }

    private function recordPaymentLog(array $payload): void
    {
        try {
            DB::table('payment_logs')->insert([
                'user_id' => $payload['user_id'],
                'ticket_id' => $payload['ticket_id'],
                'showtime_id' => $payload['showtime_id'],
                'payment_method' => $payload['payment_method'],
                'status' => $payload['status'],
                'attempted_at' => $payload['attempted_at'],
                'amount' => $payload['amount'],
                'discount_amount' => $payload['discount_amount'],
                'final_amount' => $payload['final_amount'],
                'seat_count' => $payload['seat_count'],
                'voucher_code' => $payload['voucher_code'],
                'reference_code' => $payload['reference_code'] ?? null,
                'customer_email' => $payload['customer_email'],
                'customer_phone' => $payload['customer_phone'],
                'error_message' => $payload['error_message'],
                'metadata' => json_encode($payload['metadata'] ?? [], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to persist payment log.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function createVnpayUrl(int $amount, int $ticketId): string
    {
        $vnpUrl = env('VNP_URL');
        $vnpTmnCode = env('VNP_TMN_CODE');
        $vnpHashSecret = env('VNP_HASH_SECRET');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnpTmnCode,
            'vnp_Amount' => $amount * 100,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => request()->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan ve xem phim APC-TICKET',
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => env('VNP_RETURN_URL'),
            'vnp_TxnRef' => $ticketId,
        ];

        ksort($inputData);

        $query = '';
        $hashData = '';
        $index = 0;

        foreach ($inputData as $key => $value) {
            $pair = urlencode($key) . '=' . urlencode((string) $value);
            $hashData .= $index === 0 ? $pair : '&' . $pair;
            $query .= $pair . '&';
            $index++;
        }

        $secureHash = hash_hmac('sha512', $hashData, (string) $vnpHashSecret);

        return $vnpUrl . '?' . $query . 'vnp_SecureHash=' . $secureHash;
    }

    public function vnpayReturn(Request $request)
    {
        $ticketId = (int) $request->vnp_TxnRef;

        if ($request->vnp_ResponseCode === '00') {
            DB::table('tickets')->where('id', $ticketId)->update([
                'status' => 'paid',
                'updated_at' => now()
            ]);
            $emailDelivered = $this->sendTicketConfirmationEmail($ticketId, 'vnpay');
            $ticketEmail = DB::table('tickets')->where('id', $ticketId)->value('email');

            $ticket = DB::table('tickets')->where('id', $ticketId)->first();
            if ($ticket && $ticket->user_id) {
                $user = User::find($ticket->user_id);
                $user?->notify(new BookingStatusNotification(
                    'success',
                    'Thanh toán thành công',
                    $emailDelivered && filled($ticketEmail)
                    ? 'Thanh toán thành công! Vé đã được gửi tới ' . $ticketEmail . '.'
                    : 'Thanh toán thành công. Vé đã được lưu trong tài khoản của bạn.',
                    route('account.index', ['tab' => 'tickets'])
                ));
            }

            $successMessage = $emailDelivered && filled($ticketEmail)
                ? 'Thanh toán thành công! Vé đã được gửi tới ' . $ticketEmail . '.'
                : 'Thanh toán thành công';

            return redirect()->route('account.index', ['tab' => 'tickets'])->with('payment_success', $successMessage);
        }

        $showtimeId = DB::table('tickets')->where('id', $ticketId)->value('showtime_id');
        $ticket = DB::table('tickets')->where('id', $ticketId)->first();
        if ($ticket && $ticket->user_id) {
            $user = User::find($ticket->user_id);
            $user?->notify(new BookingStatusNotification(
                'failed',
                'Thanh toán thất bại',
                'Thanh toán VNPAY không thành công. Vui lòng thử lại hoặc kiểm tra lại thông tin thanh toán.',
                $showtimeId ? route('booking.show', ['id' => $showtimeId]) : route('account.index', ['tab' => 'tickets'])
            ));
        }

        if ($showtimeId) {
            return redirect()->route('booking.show', ['id' => $showtimeId])->with('payment_error', 'Thanh toán thất bại!');
        }

        return redirect()->route('account.index', ['tab' => 'tickets'])->with('payment_error', 'Thanh toán thất bại');
    }

    protected function sendTicketConfirmationEmail(int $ticketId, string $paymentMethod): bool
    {
        $ticket = $this->buildTicketMailData($ticketId, $paymentMethod);

        if (!$ticket || blank($ticket->email)) {
            return false;
        }

        if (!blank($ticket->emailed_at)) {
            return true;
        }

        try {
            Mail::to($ticket->email)->send(new BookingTicketMail($ticket));

            DB::table('tickets')
                ->where('id', $ticketId)
                ->update([
                    'emailed_at' => now(),
                    'updated_at' => now(),
                ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('Khong the gui email ve xem phim.', [
                'ticket_id' => $ticketId,
                'email' => $ticket->email,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function buildTicketMailData(int $ticketId, string $paymentMethod): ?object
    {
        $ticket = DB::table('tickets')
            ->join('showtimes', 'showtimes.id', '=', 'tickets.showtime_id')
            ->join('movies', 'movies.id', '=', 'showtimes.movie_id')
            ->join('rooms', 'rooms.id', '=', 'showtimes.room_id')
            ->join('cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
            ->leftJoin('voucher_usages', 'voucher_usages.ticket_id', '=', 'tickets.id')
            ->where('tickets.id', $ticketId)
            ->select([
                'tickets.id',
                'tickets.fullname',
                'tickets.email',
                'tickets.phone',
                'tickets.booking_date',
                'tickets.total_price',
                'tickets.final_price',
                'tickets.reference_code',
                'tickets.ticket_code',
                'tickets.emailed_at',
                'movies.name as movie_name',
                'movies.age_limit',
                'showtimes.start_time',
                'rooms.name as room_name',
                'cinemas.name as cinema_name',
                'cinemas.address as cinema_address',
                'voucher_usages.voucher_code',
                'voucher_usages.discount_amount',
            ])
            ->first();

        if (!$ticket) {
            return null;
        }

        $seats = DB::table('ticket_details')
            ->join('seats', 'seats.id', '=', 'ticket_details.seat_id')
            ->where('ticket_details.ticket_id', $ticketId)
            ->orderBy('seats.seat_name')
            ->pluck('seats.seat_name')
            ->map(fn($seatName) => strtoupper($seatName))
            ->values()
            ->all();

        $bookingDate = $ticket->booking_date ? Carbon::parse($ticket->booking_date) : null;
        $startTime = $ticket->start_time ? Carbon::parse($ticket->start_time) : null;
        $emailedAt = $ticket->emailed_at ? Carbon::parse($ticket->emailed_at) : null;
        $discountAmount = (float) ($ticket->discount_amount ?? 0);
        $totalPrice = (float) ($ticket->total_price ?? 0);
        $storedFinalPrice = $ticket->final_price !== null ? (float) $ticket->final_price : null;

        $ticket->booking_date = $bookingDate;
        $ticket->start_time = $startTime;
        $ticket->emailed_at = $emailedAt;
        $ticket->seat_names = $seats;
        $ticket->seat_list = implode(', ', $seats);
        $ticket->discount_amount = $discountAmount;
        $ticket->final_price = $storedFinalPrice ?? max($totalPrice - $discountAmount, 0);
        $ticket->payment_method_label = match (strtolower($paymentMethod)) {
            'paypal' => 'PayPal',
            default => 'VNPay',
        };

        return $ticket;
    }

    private function generateUniqueTicketCode(): string
    {
        do {
            $code = 'CB-' . strtoupper(Str::random(8));
        } while (DB::table('tickets')->where('ticket_code', $code)->exists());

        return $code;
    }
}
