<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        return DB::table('tickets')
            ->leftJoin('showtimes', 'showtimes.id', '=', 'tickets.showtime_id')
            ->leftJoin('movies', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'showtimes.room_id')
            ->leftJoin('cinemas', 'cinemas.id', '=', 'rooms.cinema_id')
            ->leftJoin('voucher_usages', 'voucher_usages.ticket_id', '=', 'tickets.id')
            ->where('tickets.user_id', $userId)
            ->orderByDesc('tickets.booking_date')
            ->select([
                'tickets.id',
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
            ->get()
            ->map(function ($ticket) {
                $discountAmount = (float) ($ticket->discount_amount ?? 0);
                $totalPrice = (float) ($ticket->total_price ?? 0);
                $storedFinalPrice = $ticket->final_price !== null ? (float) $ticket->final_price : null;
                $ticket->booking_date = $ticket->booking_date ? Carbon::parse($ticket->booking_date) : null;
                $ticket->start_time = $ticket->start_time ? Carbon::parse($ticket->start_time) : null;
                $ticket->discount_amount = $discountAmount;
                $ticket->final_price = $storedFinalPrice ?? max($totalPrice - $discountAmount, 0);

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
}
