<?php

use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CinemaController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

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
Route::get('/movies', [MovieController::class, 'list'])->name('movies.index');

// Cinemas
Route::get('/cinemas', [CinemaController::class, 'index'])->name('cinemas.index');
Route::get('/cinemas/{id}', [CinemaController::class, 'show'])->name('cinemas.show');

// Booking
Route::get('/booking/{id}', [MovieController::class, 'show'])->name('booking.show');

// Feedback
Route::get('/feedback', function () {
    return view('feedback');
})->name('feedback');

Route::post('/feedback', function (\Illuminate\Http\Request $request) {
    return back()->with('success', 'Cảm ơn sếp đã gửi phản hồi nha!');
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
    'settings' => 'Cài đặt',
];

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () use ($adminTabs) {
    Route::get('/', function () use ($adminTabs) {
        return view('admin.home', [
            'activeTab' => 'dashboard',
            'pageTitle' => $adminTabs['dashboard'],
            'adminTabs' => $adminTabs,
        ]);
    })->name('dashboard');

    Route::get('/management', function () use ($adminTabs) {
        return view('admin.home', [
            'activeTab' => 'management',
            'pageTitle' => $adminTabs['management'],
            'adminTabs' => $adminTabs,
        ]);
    })->name('management');

    Route::get('/posts', function () use ($adminTabs) {
        return view('admin.home', [
            'activeTab' => 'posts',
            'pageTitle' => $adminTabs['posts'],
            'adminTabs' => $adminTabs,
        ]);
    })->name('posts');

    Route::get('/actions', [VoucherController::class, 'index'])->name('actions');
    Route::post('/actions/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('/actions/vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/actions/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');

    Route::get('/settings', function () use ($adminTabs) {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.home', [
            'activeTab' => 'settings',
            'pageTitle' => $adminTabs['settings'],
            'adminTabs' => $adminTabs,
            'settings' => $settings,
        ]);
    })->name('settings');

    Route::post('/settings', function (\Illuminate\Http\Request $request) {
        $data = $request->except('_token');
        
        // Chế độ checkbox
        if (!isset($data['email_notification_active'])) {
            $data['email_notification_active'] = 'false';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Cấu hình hệ thống đã được cập nhật thành công!');
    })->name('settings.update');

    // Admin Management Resources
    Route::resource('movies', App\Http\Controllers\Admin\MovieController::class);
    Route::resource('cinemas', App\Http\Controllers\Admin\CinemaController::class);
    Route::resource('showtimes', App\Http\Controllers\Admin\ShowtimeController::class);
    Route::resource('tickets', App\Http\Controllers\Admin\TicketController::class);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
});



