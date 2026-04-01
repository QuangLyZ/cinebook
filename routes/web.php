<?php

use App\Http\Controllers\SendEmailController;
use Illuminate\Support\Facades\Route;
Route::get('/sendEmail', [SendEmailController::class, 'send'])->name('sendEmail');
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Google Socialite Login Routes
Route::get('/auth/google', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.post');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

Route::get('/movies', function () {
    return view('movies.index');
})->name('movies.index');

Route::get('/booking/{id}', function () {
    return view('booking.show');
})->name('booking.show');

Route::get('/feedback', function () {
    return view('feedback');
})->name('feedback');

Route::post('/feedback', function (\Illuminate\Http\Request $request) {
    return back()->with('success', 'Cảm ơn sếp đã gửi phản hồi nha!');
});

use App\Http\Controllers\OtpController;

// OTP Routes
Route::get('/otp/send', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.form');
Route::post('/otp/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
