<?php

use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
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

// Cinemas (Thêm route này để sửa lỗi Route [cinemas.index] not defined)
Route::get('/cinemas', function () {
    return view('home'); // Tạm thời trỏ về home hoặc trang rạp nếu có
})->name('cinemas.index');

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
