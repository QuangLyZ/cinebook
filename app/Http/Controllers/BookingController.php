<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

            abort_if(! $showtime, 404);

            $seats = DB::table('seats')
                ->where('room_id', $showtime->room_id)
                ->orderBy('seat_name')
                ->select(['id', 'seat_name', 'seat_type'])
                ->get();

            $takenSeatNames = DB::table('ticket_details')
                ->join('tickets', 'tickets.id', '=', 'ticket_details.ticket_id')
                ->join('seats', 'seats.id', '=', 'ticket_details.seat_id')
                ->where('tickets.showtime_id', $showtimeId)
                ->pluck('seats.seat_name')
                ->all();

            if ($request->user()) {
                $availableVouchers = $this->loadAvailableVouchers();
            }
        } catch (QueryException) {
            // Render the fallback UI below when the schema is unavailable.
        }

        $startTime = $showtime?->start_time ? Carbon::parse($showtime->start_time) : null;
        $seatMap = $this->buildSeatMap($seats);
        $voucherPayload = $availableVouchers->map(fn ($voucher) => [
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
                ? 'Hôm nay'
                : ($startTime ? ucfirst($startTime->translatedFormat('l, d/m/Y')) : 'Lịch chiếu đang cập nhật'),
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
        if (! $request->user()) {
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
                'error_message' => 'Người dùng chưa đăng nhập.',
                'metadata' => [
                    'seat_names' => (array) $request->input('seat_names', []),
                ],
            ]);

            return response()->json([
                'message' => 'Bạn cần đăng nhập để hoàn tất thanh toán và lưu lịch sử vé.',
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
                if (! $showtime) {
                    throw ValidationException::withMessages([
                        'showtime' => 'Suất chiếu không còn tồn tại.',
                    ]);
                }

                $seatNames = collect($data['seat_names'])
                    ->map(fn ($seat) => strtoupper(trim($seat)))
                    ->unique()
                    ->values();

                $seats = DB::table('seats')
                    ->where('room_id', $showtime->room_id)
                    ->whereIn('seat_name', $seatNames)
                    ->select(['id', 'seat_name'])
                    ->get();

                if ($seats->count() !== $seatNames->count()) {
                    throw ValidationException::withMessages([
                        'seat_names' => 'Một hoặc nhiều ghế không hợp lệ cho phòng chiếu này.',
                    ]);
                }

                $alreadyBooked = DB::table('ticket_details')
                    ->join('tickets', 'tickets.id', '=', 'ticket_details.ticket_id')
                    ->where('tickets.showtime_id', $showtimeId)
                    ->whereIn('ticket_details.seat_id', $seats->pluck('id'))
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

                $ticketId = DB::table('tickets')->insertGetId([
                    'user_id' => $request->user()->id,
                    'showtime_id' => $showtimeId,
                    'fullname' => $data['fullname'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'booking_date' => now(),
                    'total_price' => $baseTotal,
                    'voucher_id' => $voucher?->id,
                    'discount_amount' => $discountAmount,
                    'final_price' => $finalPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $seatRows = $seats->map(fn ($seat) => [
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
                    'reference_code' => 'PAY-'.now()->format('YmdHis').'-'.$ticketId,
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

            return response()->json([
                'message' => 'Thanh toán thành công. Vé đã được lưu vào tài khoản của bạn.',
                'redirect_url' => route('account.index', ['tab' => 'tickets']),
                'ticket' => $result,
            ]);
        } catch (ValidationException $exception) {
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
                'message' => 'Thanh toán chưa hoàn tất. Vui lòng thử lại.',
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

        if (! $voucher) {
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

        if (! is_null($voucher->usage_limit) && (int) $voucher->used_count >= (int) $voucher->usage_limit) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher đã hết lượt sử dụng.',
            ]);
        }

        return $voucher;
    }

    protected function calculateDiscount(object $voucher, int $baseTotal): int
    {
        if (! is_null($voucher->discount_rate)) {
            return (int) round($baseTotal * ((int) $voucher->discount_rate / 100));
        }

        return min((int) $voucher->discount_value, $baseTotal);
    }

    protected function buildSeatMap(Collection $seats): Collection
    {
        if ($seats->isEmpty()) {
            return collect(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'])->map(function ($row) {
                return collect(range(1, 14))->map(fn ($number) => (object) [
                    'id' => null,
                    'seat_name' => $row.$number,
                    'seat_type' => 'standard',
                ]);
            });
        }

        return $seats
            ->groupBy(fn ($seat) => preg_replace('/\d+$/', '', $seat->seat_name) ?: 'A')
            ->sortKeys()
            ->map(fn ($group) => $group->sortBy(function ($seat) {
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
}
