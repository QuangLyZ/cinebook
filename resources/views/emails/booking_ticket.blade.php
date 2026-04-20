@php
    $bookingDate = $ticket->booking_date?->format('d/m/Y H:i');
    $showDate = $ticket->start_time?->format('d/m/Y');
    $showTime = $ticket->start_time?->format('H:i');
    $ageLimit = $ticket->age_limit ? 'T'.$ticket->age_limit : 'Dang cap nhat';
    $emailedAt = $ticket->emailed_at?->format('d/m/Y H:i');
    $displayTicketCode = $ticket->ticket_code ?: ('CB-' . str_pad((string) $ticket->id, 8, '0', STR_PAD_LEFT));
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé xem phim CineBook</title>
</head>
<body style="margin:0;padding:0;background:#0f172a;font-family:Arial,Helvetica,sans-serif;color:#e5e7eb;">
    <div style="max-width:720px;margin:0 auto;padding:32px 16px;">
        <div style="background:linear-gradient(135deg,#7f1d1d,#111827);border-radius:24px;padding:32px 28px;box-shadow:0 20px 45px rgba(0,0,0,0.35);">
            <div style="font-size:12px;letter-spacing:0.24em;text-transform:uppercase;color:#fca5a5;font-weight:700;">CineBook Ticket</div>
            <h1 style="margin:14px 0 10px;font-size:30px;line-height:1.25;color:#ffffff;">Đặt vé thành công</h1>
            <p style="margin:0;font-size:15px;line-height:1.7;color:#d1d5db;">
                Chao {{ $ticket->fullname ?: 'ban' }}, thong tin ve cua ban da san sang. Vui long den rap dung gio va mang theo email nay khi can doi chieu.
            </p>

            <div style="margin-top:24px;border-radius:20px;background:rgba(15,23,42,0.75);padding:22px;border:1px solid rgba(255,255,255,0.08);">
                <div style="font-size:13px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.2em;">Ma ve</div>
                <div style="margin-top:8px;font-size:34px;font-weight:800;color:#fef2f2;">{{ $displayTicketCode }}</div>
                <div style="margin-top:10px;font-size:15px;color:#fca5a5;">{{ $ticket->movie_name }}</div>
            </div>

            <div style="margin-top:24px;background:#111827;border-radius:20px;padding:8px 0;border:1px solid rgba(255,255,255,0.08);">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;width:42%;">Khach hang</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->fullname ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Email</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->email ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">So dien thoai</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->phone ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Phim</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->movie_name ?: '--' }} ({{ $ageLimit }})</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Cum rap</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->cinema_name ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Dia chi rap</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->cinema_address ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Phong chieu</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->room_name ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Ngay chieu</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $showDate ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Gio chieu</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $showTime ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Ghe</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->seat_list ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Dat luc</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $bookingDate ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Ma tham chieu</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->reference_code ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Phuong thuc thanh toan</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->payment_method_label }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Email nhan ve</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->email ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Gui email luc</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $emailedAt ?: '--' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Voucher</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ $ticket->voucher_code ?: 'Khong ap dung' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Tong truoc giam</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ number_format((float) $ticket->total_price, 0, ',', '.') }} VND</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Giam gia</td>
                        <td style="padding:14px 22px;color:#ffffff;font-weight:600;">{{ number_format((float) $ticket->discount_amount, 0, ',', '.') }} VND</td>
                    </tr>
                    <tr>
                        <td style="padding:14px 22px;color:#94a3b8;">Thanh tien</td>
                        <td style="padding:14px 22px;color:#fca5a5;font-size:18px;font-weight:800;">{{ number_format((float) $ticket->final_price, 0, ',', '.') }} VND</td>
                    </tr>
                </table>
            </div>

            <p style="margin:22px 0 0;font-size:13px;line-height:1.7;color:#9ca3af;">
                Luu y: vui long co mat truoc gio chieu it nhat 15 phut. Neu can doi chieu, ban co the cung cap ma ve <strong style="color:#ffffff;">{{ $displayTicketCode }}</strong> cho nhan vien.
            </p>
        </div>
    </div>
</body>
</html>
