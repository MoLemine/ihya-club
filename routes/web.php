<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', FeedController::class)->name('home');
Route::get('/requests-feed', FeedController::class)->name('feed.index');
Route::get('/requests/create', [BloodRequestController::class, 'create'])->name('requests.create');
Route::get('/me', [ProfileController::class, 'me'])->name('profile.me');
Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');
Route::post('/guest-profile', [ProfileController::class, 'storeGuest'])->name('guest-profile.store');
Route::post('/account/upgrade', [AccountController::class, 'upgrade'])->name('account.upgrade');
Route::post('/requests', [BloodRequestController::class, 'store'])->name('requests.store');
Route::post('/requests/{bloodRequest}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/requests/{bloodRequest}/donate', [DonationController::class, 'store'])->name('donations.store');
Route::post('/requests/{bloodRequest}/share', [DonationController::class, 'share'])->name('donations.share');
Route::patch('/donations/{donation}/complete', [DonationController::class, 'complete'])->name('donations.complete');
Route::patch('/donations/{donation}/cancel', [DonationController::class, 'cancel'])->name('donations.cancel');
Route::post('/locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'create'])->name('login');
    Route::post('/login', [UserAuthController::class, 'store'])->name('login.store');
    Route::get('/register', [UserAuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('register.store');
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
});

Route::middleware('auth')->group(function () {
    Route::patch('/me', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/{notification}', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/logout', [UserAuthController::class, 'destroy'])->name('logout');
    Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{bloodRequest}', [AdminRequestController::class, 'update'])->name('requests.update');

    Route::middleware('superadmin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });
});
