<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Notifications\ShowtimeReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendShowtimeReminders extends Command
{
    protected $signature = 'notifications:send-showtime-reminders';

    protected $description = 'Gửi thông báo nhắc nhở trước giờ chiếu 2 tiếng cho khách đã đặt vé.';

    public function handle(): int
    {
        $now = Carbon::now();
        $cutoff = $now->copy()->addHours(2);

        $tickets = Ticket::with(['user', 'showtime.movie', 'showtime.room.cinema'])
            ->whereNull('reminded_at')
            ->whereHas('showtime', function ($query) use ($now, $cutoff) {
                $query->whereBetween('start_time', [$now, $cutoff]);
            })
            ->get();

        foreach ($tickets as $ticket) {
            if (!$ticket->user || !$ticket->showtime) {
                continue;
            }

            $movieName = $ticket->showtime->movie->name ?? 'Phim';
            $cinemaName = $ticket->showtime->room->cinema->name ?? 'rạp chiếu';
            $startTime = Carbon::parse($ticket->showtime->start_time)->format('H:i, d/m/Y');

            $ticket->user->notify(new ShowtimeReminderNotification(
                'Nhắc lịch chiếu sắp tới',
                "Suất chiếu của {$movieName} tại {$cinemaName} sẽ bắt đầu lúc {$startTime}. Nhớ đến sớm để nhận vé nhé.",
                route('account.index', ['tab' => 'tickets'])
            ));

            $ticket->update(['reminded_at' => now()]);
        }

        $this->info('Đã gửi thông báo nhắc nhở cho ' . $tickets->count() . ' vé.');

        return 0;
    }
}
