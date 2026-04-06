<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OtpController extends Controller
{
    // B1: Gửi mã OTP vào Email
    public function sendOtp(Request $request)
    {
        // Gửi lại mã OTP khi đang ở trang xác nhận
        $email = Session::get('verify_email');

        if (!$email || !Cache::has('register_data_' . $email)) {
             return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn hoặc không tồn tại, sếp đăng ký lại từ đầu nha.');
        }

        $userData = Cache::get('register_data_' . $email);
        $newOtpCode = rand(100000, 999999);
        $userData['otp'] = $newOtpCode;

        // Cập nhật lại cache với OTP mới, reset 5 phút
        Cache::put('register_data_' . $email, $userData, now()->addMinutes(5));

        // Bắn email giao diện HTML xịn xò
        try {
            Mail::send('emails.otp', ['otp' => $newOtpCode, 'userName' => $userData['name']], function ($message) use ($email) {
                $message->to($email)->subject('🔒 [CINEBOOK] Mã Xác Thực OTP Của Bạn');
            });
        } catch (\Exception $e) {
            // bỏ qua lỗi mail
        }

        // Chuyển hướng người dùng qua trang nhập mã
        return redirect()->route('otp.form')->with('success', 'Đã gửi lại mã OTP mới. Vui lòng kiểm tra email của bạn!');
    }

    // B2: Hiện giao diện nhập OTP
    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    // B3: Kiểm tra OTP user nhập vào có đúng không
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // Lấy email đang cần xác thực từ Session
        $email = Session::get('verify_email');

        if (!$email) {
            return redirect()->route('register')->with('error', 'Không tìm thấy phiên xác thực. Bạn vui lòng đăng ký lại nha!');
        }

        // Lấy thông tin khách hàng từ Cache
        $userData = Cache::get('register_data_' . $email);

        // Kiểm tra xem khách có đăng ký chưa và mã nhập vào có giống mã trong Cache không
        if ($userData && $request->otp == $userData['otp']) {
            // Đúng thì CHÍNH THỨC TẠO USER VÀO DATABASE
            $user = User::create([
                'fullname' => $userData['fullname'],
                'email' => $userData['email'],
                'password' => $userData['password'],
            ]);

            // Xóa rác Cache và Session cho sạch sẽ
            Cache::forget('register_data_' . $email);
            Session::forget('verify_email');

            // Cho đăng nhập luôn cho nóng
            Auth::login($user);

            // Hiện thông báo mừng rỡ
            return redirect()->route('home')->with('success', 'Tuyệt vời ông mặt trời! Đăng ký & Xác thực thành công rực rỡ! 🎉');
        }

        // Khôi phục lại session verify_email để họ nhập lại cho tiện
        Session::put('verify_email', $email);

        // Sai thì bắt nhập lại
        return back()->with('error', 'Mã OTP trật lất rồi bạn ơi! Kiểm tra lại email nha! 😅');
    }
}
