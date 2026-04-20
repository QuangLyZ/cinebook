<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'showtime.movie', 'showtime.room.cinema'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('fullname', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhere('phone', 'like', "%$q%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('admin.tickets.index', [
            'tickets'   => $tickets,
            'filters'   => $request->only(['q', 'date']),
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Vé',
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'showtime.movie', 'showtime.room.cinema', 'showtime.subtitle', 'details.seat']);
        return view('admin.tickets.show', [
            'ticket'    => $ticket,
            'activeTab' => 'management',
            'pageTitle' => 'Chi tiết Vé #' . $ticket->id,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = Ticket::with(['user', 'showtime.movie', 'showtime.room.cinema'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('fullname', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhere('phone', 'like', "%$q%");
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $tickets = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tickets_' . now()->format('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Mã vé', 'ID', 'Khách hàng', 'Email', 'SĐT',
                'Phim', 'Rạp', 'Phòng', 'Suất chiếu',
                'Số ghế', 'Tổng tiền (VND)', 'Ngày đặt'
            ]);

            foreach ($tickets as $ticket) {
                $showtime = $ticket->showtime;
                fputcsv($handle, [
                    $ticket->ticket_code ?: ('CB-' . str_pad((string) $ticket->id, 8, '0', STR_PAD_LEFT)),
                    $ticket->id,
                    $ticket->fullname ?? ($ticket->user?->name ?? 'N/A'),
                    $ticket->email ?? ($ticket->user?->email ?? 'N/A'),
                    $ticket->phone ?? 'N/A',
                    $showtime?->movie?->name ?? 'N/A',
                    $showtime?->room?->cinema?->name ?? 'N/A',
                    $showtime?->room?->name ?? 'N/A',
                    $showtime?->start_time?->format('d/m/Y H:i') ?? 'N/A',
                    $ticket->details()->count(),
                    number_format((float) $ticket->total_price, 0, ',', '.'),
                    $ticket->booking_date?->format('d/m/Y H:i') ?? 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()  {}
    public function store(Request $request) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('admin.tickets.index')
            ->with('success', 'Đã xóa vé thành công!');
    }
}
