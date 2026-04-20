<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Mail\SendOtpMail;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    protected function redirectAfterLogin(User $user)
    {
        return $user->admin_role ? route('admin.dashboard') : route('home');
    }

    // Bắt đầu quy trình đăng nhập Google
    public function redirectToGoogle()
    {
        // GRACE: Bat buoc dung stateless de tranh loi session mismatch tren local
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Xử lý dữ liệu Google trả về
    public function handleGoogleCallback()
    {
        try {
            // GRACE: Dung stateless de bo qua kiem tra session state (rat de loi tren local)
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Tìm xem email này đã tồn tại trong hệ thống chưa
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // Nếu chưa có, tạo tài khoản mới toanh cho khách
                $user = $this->createGoogleUser([
                    'fullname' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    // Tạo một mật khẩu ảo siêu dài ngẫu nhiên vì khách đăng nhập bằng Google
                    'password' => Hash::make(Str::random(24)),
                    'phone' => '0000000000', // Đặt số điện thoại mặc định (do database bắt buộc)
                    'google_id' => $googleUser->getId(),
                    'admin_role' => false,
                ]);
            }
            
            // Cho đăng nhập luôn
            Auth::login($user);
            return redirect()->to($this->redirectAfterLogin($user))->with('success', 'Đăng nhập bằng Google thành công.');
            
        } catch (\Exception $e) {
            \Log::error('--- BUG DETECTOR: GOOGLE LOGIN FAILED ---');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            
            // Neu co phan hoi tu Guzzle, ghi lai luon de xem loi SSL hay loi gi khac
            if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                \Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            
            \Log::error('Trace: ' . $e->getTraceAsString());
            \Log::error('--- END BUG DETECTOR ---');

            $errorDetail = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            return redirect()->route('login')->withErrors(['google_error' => 'Lỗi Google Login: ' . $errorDetail]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập Email hoặc Số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        // Hỗ trợ đăng nhập bằng cả Email hoặc Số điện thoại
        $login = trim($request->email);

        $credentials = ['password' => $request->password];
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $login;
        } else {
            $credentials['phone'] = $login;
        }

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            /** @var User $user */
            $user = Auth::user();
            return redirect()->to($this->redirectAfterLogin($user))->with('success', 'Đăng nhập thành công.');
        }

        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đăng xuất thành công.');
    }

    public function register(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:Users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // 2. Tạo mã OTP ngẫu nhiên 6 số
        $otpCode = rand(100000, 999999);

        // 3. Gói ghém toàn bộ thông tin đăng ký của khách + mã OTP vào một cục
        $userData = [
            'fullname' => trim($request->name),
            'email' => trim($request->email),
            'phone' => $request->phone ? trim($request->phone) : null,
            'password' => Hash::make($request->password),
            'admin_role' => false,
            'otp' => $otpCode
        ];

        // 4. Cất cục thông tin này vào Tủ Khóa (Cache), cài giờ nổ là 5 phút
        // Lưu ý: Mình CHƯA lưu vào Database để tránh rác nếu họ không nhập OTP
        Cache::put('register_data_' . $request->email, $userData, now()->addMinutes(5));

        // 5. Gửi Email
        try {
            Mail::to(trim($request->email))->send(new SendOtpMail($otpCode, trim($request->name)));
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi mail OTP: ' . $e->getMessage());
        }

        // 6. Chuyển hướng sang trang nhập OTP, kèm theo cái email để biết đang xác thực cho ai
        \Illuminate\Support\Facades\Session::put('verify_email', trim($request->email));
        return redirect()->route('otp.form')
            ->with('success', 'Mã xác thực OTP đã được gửi. Vui lòng kiểm tra email của bạn để tiếp tục!');
    }

    protected function createGoogleUser(array $attributes): User
    {
        try {
            return User::create($attributes);
        } catch (QueryException $exception) {
            if (! $this->isUsersPrimaryKeySequenceError($exception)) {
                throw $exception;
            }

            $this->syncUsersPrimaryKeySequence();

            return User::create($attributes);
        }
    }

    protected function isUsersPrimaryKeySequenceError(QueryException $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, 'duplicate key value violates unique constraint "Users_pkey"')
            || str_contains($message, 'duplicate key value violates unique constraint "users_pkey"');
    }

    protected function syncUsersPrimaryKeySequence(): void
    {
        DB::statement(<<<'SQL'
            SELECT setval(
                pg_get_serial_sequence('"Users"', 'id'),
                COALESCE((SELECT MAX(id) FROM "Users"), 0) + 1,
                false
            )
        SQL);
    }
}
