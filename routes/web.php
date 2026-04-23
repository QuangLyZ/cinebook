<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\{
    SendEmailController,
    AccountController,
    BookingController,
    MovieController,
    NotificationController,
    OtpController,
    AuthController,
    CinemaController,
    PostController,
    ReviewController
};

use App\Http\Controllers\Admin\{
    DashboardController,
    VoucherController,
    PostController as AdminPostController,
    MovieController as AdminMovieController,
    ReviewController as AdminReviewController,
    CinemaController as AdminCinemaController,
    ShowtimeController,
    TicketController,
    UserController
};

# =========================
# PUBLIC ROUTES
# =========================

// Home
Route::get('/', [MovieController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google login
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Register
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Forgot password
Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('forgot-password');

// Movies
Route::get('/movies', [MovieController::class, 'list'])->name('movies.index');
Route::get('/movies/suggestions', [MovieController::class, 'suggestions'])->name('movies.suggestions');
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');

// Cinemas
Route::get('/cinemas', [CinemaController::class, 'theaters'])->name('cinemas.index');
Route::get('/cinemas/{id}', [CinemaController::class, 'show'])->name('cinemas.show');

// Booking
Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking/{id}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/vnpay-return', [BookingController::class, 'vnpayReturn'])->name('vnpay.return');

// Posts (USER)
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::post('/upload-image', [PostController::class, 'uploadImage'])->name('upload.image');

// Reviews
Route::get('/movies/{movie}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/movies/{movie}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

// Email
Route::get('/sendEmail', [SendEmailController::class, 'send'])->name('sendEmail');

// OTP
Route::get('/otp/send', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.form');
Route::post('/otp/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');

# =========================
# AUTH USER ROUTES
# =========================

Route::middleware('auth')->group(function () {

    // Account
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::post('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');

    // OTP password
    Route::get('/account/password/otp', [AccountController::class, 'showPasswordOtpForm'])->name('account.password.otp');
    Route::post('/account/password/otp', [AccountController::class, 'verifyPasswordOtp'])->name('account.password.otp.verify');
    Route::get('/account/password/resend-otp', [AccountController::class, 'resendPasswordOtp'])->name('account.password.otp.resend');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read/{notification}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});

# =========================
# ADMIN ROUTES (CLEAN)
# =========================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Posts (ADMIN ONLY)
        Route::resource('posts', AdminPostController::class);

        // Custom post actions
        Route::patch('posts/{post}/toggle', [AdminPostController::class, 'toggle'])
            ->name('posts.toggle');

        Route::post('posts/upload-thumbnail', [AdminPostController::class, 'uploadThumbnail'])
            ->name('posts.upload-thumbnail');

        // Voucher
        Route::get('actions', [VoucherController::class, 'index'])->name('actions');
        Route::post('actions/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::put('actions/vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
        Route::delete('actions/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');

        // Feedback
        Route::get('feedback', function () {
            $feedbacks = \App\Models\Feedback::with('user')->latest()->get();
            return view('admin.home', compact('feedbacks'));
        })->name('feedback');

        Route::delete('feedback/{feedback}', function (\App\Models\Feedback $feedback) {
            $feedback->delete();
            return back()->with('success', 'Deleted');
        })->name('feedback.destroy');

        // Management resources
        Route::resource('movies', AdminMovieController::class);
        Route::resource('reviews', AdminReviewController::class)->only(['index', 'destroy']);
        Route::resource('cinemas', AdminCinemaController::class);
        Route::resource('showtimes', ShowtimeController::class);
        Route::resource('tickets', TicketController::class);
        Route::get('tickets-export', [TicketController::class, 'exportCsv'])->name('tickets.export');
        Route::resource('users', UserController::class);
});
