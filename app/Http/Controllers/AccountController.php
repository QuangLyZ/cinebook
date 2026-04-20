<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tickets = collect();
        $availableVouchers = collect();
        $voucherUsages = collect();
        $stats = [
            'ticket_count' => 0,
            'ticket_spent' => 0,
            'available_vouchers' => 0,
            'used_vouchers' => 0,
        ];

        try {
            $tickets = $this->loadTickets($user->id);
            $availableVouchers = $this->loadAvailableVouchers();
            $voucherUsages = $this->loadVoucherUsages($user->id);

            $stats = [
                'ticket_count' => $tickets->count(),
                'ticket_spent' => $tickets->sum('final_price'),
                'available_vouchers' => $availableVouchers->count(),
                'used_vouchers' => $voucherUsages->count(),
            ];
        } catch (QueryException) {
            // Keep the page renderable even when some tables are not ready locally.
        }

        return view('account.index', [
            'tickets' => $tickets,
            'availableVouchers' => $availableVouchers,
            'voucherUsages' => $voucherUsages,
            'accountStats' => $stats,
            'accountTab' => $request->query('tab', 'tickets'),
        ]);
    }

    protected function loadTickets(int $userId): Collection
    {
        $tickets = DB::table('tickets')
            ->leftJoin('showtimes', 'showtimes.id', '=', 'tickets.showtime_id')
            ->leftJoin('movies', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'showtimes.room_id')
            ->leftJoin('cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
            ->leftJoin('voucher_usages', 'voucher_usages.ticket_id', '=', 'tickets.id')
            ->where('tickets.user_id', $userId)
            ->orderByDesc('tickets.booking_date')
            ->select([
                'tickets.id',
                'tickets.ticket_code',
                'tickets.booking_date',
                'tickets.total_price',
                'tickets.final_price',
                'tickets.fullname',
                'tickets.email',
                'tickets.phone',
                'movies.name as movie_name',
                'showtimes.start_time',
                'rooms.name as room_name',
                'cinemas.name as cinema_name',
                'voucher_usages.voucher_code',
                'voucher_usages.discount_amount',
            ])
            ->get();

        $ticketIds = $tickets->pluck('id')->all();
        $seatsByTicket = collect();
        
        if (!empty($ticketIds)) {
            $seatsByTicket = DB::table('ticket_details')
                ->join('seats', 'seats.id', '=', 'ticket_details.seat_id')
                ->whereIn('ticket_details.ticket_id', $ticketIds)
                ->select(['ticket_details.ticket_id', 'seats.seat_name'])
                ->get()
                ->groupBy('ticket_id');
        }

        return $tickets->map(function ($ticket) use ($seatsByTicket) {
                $discountAmount = (float) ($ticket->discount_amount ?? 0);
                $totalPrice = (float) ($ticket->total_price ?? 0);
                $storedFinalPrice = $ticket->final_price !== null ? (float) $ticket->final_price : null;
                $ticket->booking_date = $ticket->booking_date ? Carbon::parse($ticket->booking_date) : null;
                $ticket->start_time = $ticket->start_time ? Carbon::parse($ticket->start_time) : null;
                $ticket->discount_amount = $discountAmount;
                $ticket->final_price = $storedFinalPrice ?? max($totalPrice - $discountAmount, 0);

                $seats = $seatsByTicket->get($ticket->id) ?? collect();
                $ticket->seat_names = collect($seats)->pluck('seat_name')->join(', ');

                return $ticket;
            });
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
            ->select([
                'id',
                'code',
                'discount_value',
                'discount_rate',
                'description',
                'starts_at',
                'expires_at',
                'usage_limit',
                'used_count',
            ])
            ->get()
            ->map(function ($voucher) {
                $voucher->starts_at = $voucher->starts_at ? Carbon::parse($voucher->starts_at) : null;
                $voucher->expires_at = $voucher->expires_at ? Carbon::parse($voucher->expires_at) : null;

                return $voucher;
            });
    }

    protected function loadVoucherUsages(int $userId): Collection
    {
        return DB::table('voucher_usages')
            ->leftJoin('tickets', 'tickets.id', '=', 'voucher_usages.ticket_id')
            ->leftJoin('showtimes', 'showtimes.id', '=', 'tickets.showtime_id')
            ->leftJoin('movies', 'movies.id', '=', 'showtimes.movie_id')
            ->where('voucher_usages.user_id', $userId)
            ->orderByDesc('voucher_usages.used_at')
            ->select([
                'voucher_usages.id',
                'voucher_usages.voucher_code',
                'voucher_usages.discount_amount',
                'voucher_usages.used_at',
                'voucher_usages.ticket_id',
                'movies.name as movie_name',
            ])
            ->get()
            ->map(function ($usage) {
                $usage->used_at = $usage->used_at ? Carbon::parse($usage->used_at) : null;

                return $usage;
            });
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:Users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:Users,phone,' . $user->id,
        ], [
            'fullname.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng bởi người khác.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
        ]);

        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Đã cập nhật thông tin cá nhân thành công.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải dài ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        // Tạo OTP
        $otpCode = rand(100000, 999999);
        $cacheKey = 'password_reset_data_' . $user->id;

        Cache::put($cacheKey, [
            'otp' => $otpCode,
            'new_password' => Hash::make($request->new_password)
        ], now()->addMinutes(5));

        // Gửi OTP email
        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $user->fullname ?: $user->name));
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi mail OTP đổi mật khẩu: ' . $e->getMessage());
        }

        return redirect()->route('account.password.otp')->with('success', 'Mã OTP đã được gửi vào email của bạn. Vui lòng kiểm tra để xác nhận đổi mật khẩu.');
    }

    public function showPasswordOtpForm(Request $request)
    {
        $user = $request->user();
        if (!Cache::has('password_reset_data_' . $user->id)) {
            return redirect()->route('account.index')->with('error', 'Phiên đổi mật khẩu đã hết hạn hoặc không tồn tại.');
        }

        return view('account.verify-otp');
    }

    public function verifyPasswordOtp(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $cacheKey = 'password_reset_data_' . $user->id;
        $data = Cache::get($cacheKey);

        if ($data && $request->otp == $data['otp']) {
            $user->password = $data['new_password'];
            $user->save();

            Cache::forget($cacheKey);

            return redirect()->route('account.index', ['tab' => 'profile'])->with('success', 'Đổi mật khẩu thành công!');
        }

        return back()->with('error', 'Mã OTP không chính xác. Vui lòng thử lại.');
    }

    public function resendPasswordOtp(Request $request)
    {
        $user = $request->user();
        $cacheKey = 'password_reset_data_' . $user->id;

        if (!Cache::has($cacheKey)) {
            return redirect()->route('account.index')->with('error', 'Phiên đổi mật khẩu đã hết hạn.');
        }

        $data = Cache::get($cacheKey);
        $newOtpCode = rand(100000, 999999);
        $data['otp'] = $newOtpCode;

        Cache::put($cacheKey, $data, now()->addMinutes(5));

        try {
            Mail::send('emails.otp', ['otp' => $newOtpCode, 'userName' => $user->fullname ?: $user->name], function ($message) use ($user) {
                $message->to($user->email)->subject('🔒 [CINEBOOK] Mã Xác Thực OTP Đổi Mật Khẩu');
            });
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi mail re-OTP đổi mật khẩu: ' . $e->getMessage());
        }

        return back()->with('success', 'Mã OTP mới đã được gửi lại vào email của bạn.');
    }
}
