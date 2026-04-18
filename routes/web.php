<?php

use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CinemaController;
use App\Models\Setting;
use App\Models\Feedback;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\DashboardController;

Route::post('/upload-image', [App\Http\Controllers\PostController::class, 'uploadImage'])
    ->name('upload.image');
Route::get('/sendEmail', [SendEmailController::class, 'send'])->name('sendEmail');

// Trang chủ
Route::get('/', [MovieController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Socialite Login Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

// Movies
Route::get('/movies/suggestions', [MovieController::class, 'suggestions'])->name('movies.suggestions');
Route::get('/movies', [MovieController::class, 'list'])->name('movies.index');

// Cinemas
Route::get('/cinemas', [CinemaController::class, 'theaters'])->name('cinemas.index');
Route::get('/cinemas/{id}', [CinemaController::class, 'show'])->name('cinemas.show');

// Booking
Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking/{id}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/vnpay-return', [BookingController::class, 'vnpayReturn'])->name('vnpay.return');

// Feedback
Route::get('/feedback', function () {
    return view('feedback');
})->name('feedback');

Route::post('/feedback', function (\Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return back()->with('error', 'Vui lòng đăng nhập để gửi phản hồi!');
    }

    \App\Models\Feedback::create([
        'user_id' => Auth::id(),
        'title' => $request->topic,
        'context' => $request->message,
    ]);

    return back()->with('success', 'Cảm ơn sếp đã gửi phản hồi nha! Chúng tôi sẽ xem xét sớm nhất.');
});

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
});

// OTP Routes
Route::get('/otp/send', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.form');
Route::post('/otp/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');

$adminTabs = [
    'dashboard' => 'Dashboard',
    'management' => 'Quản lý',
    'posts' => 'Bài viết',
    'actions' => 'Action',
    'feedback' => 'Ý kiến phản hồi',
];

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () use ($adminTabs) {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/management', function () use ($adminTabs) {
        return view('admin.home', [
            'activeTab' => 'management',
            'pageTitle' => $adminTabs['management'],
            'adminTabs' => $adminTabs,
        ]);
    })->name('management');

    Route::get('/actions', [VoucherController::class, 'index'])->name('actions');
    Route::post('/actions/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('/actions/vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/actions/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::post('/posts/upload-thumbnail', [App\Http\Controllers\Admin\PostController::class, 'uploadThumbnail'])->name('posts.upload-thumbnail');

    Route::get('/feedback', function () use ($adminTabs) {
        $feedbacks = \App\Models\Feedback::with('user')->latest()->get();
        return view('admin.home', [
            'activeTab' => 'feedback',
            'pageTitle' => $adminTabs['feedback'],
            'adminTabs' => $adminTabs,
            'feedbacks' => $feedbacks,
        ]);
    })->name('feedback');

    Route::post('/feedback/{feedback}/reply', function (\Illuminate\Http\Request $request, \App\Models\Feedback $feedback) {
        $request->validate(['reply_message' => 'required|string']);

        if ($feedback->user && $feedback->user->email) {
            \Illuminate\Support\Facades\Mail::to($feedback->user->email)->send(
                new \App\Mail\FeedbackReplyMail($feedback->title, $request->reply_message)
            );
            return back()->with('success', 'Đã gửi email phản hồi thành công đến ' . $feedback->user->email);
        }

        return back()->with('error', 'Người dùng này không có địa chỉ email hợp lệ.');
    })->name('feedback.reply');

    Route::delete('/feedback/{feedback}', function (\App\Models\Feedback $feedback) {
        $feedback->delete();
        return back()->with('success', 'Đã xóa phản hồi thành công.');
    })->name('feedback.destroy');

    // Admin Management Resources
    Route::resource('movies', App\Http\Controllers\Admin\MovieController::class);
    Route::resource('cinemas', App\Http\Controllers\Admin\CinemaController::class);
    Route::resource('showtimes', App\Http\Controllers\Admin\ShowtimeController::class);
    Route::resource('tickets', App\Http\Controllers\Admin\TicketController::class);
    Route::get('/tickets-export', [App\Http\Controllers\Admin\TicketController::class, 'exportCsv'])->name('tickets.export');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
});
