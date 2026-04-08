<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\DonationApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\RequestApiController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthTokenController::class, 'store']);

Route::get('/requests', [RequestApiController::class, 'index']);
Route::post('/requests', [RequestApiController::class, 'store']);
Route::get('/requests/{request}', [RequestApiController::class, 'show']);
Route::get('/requests/{request}/comments', [CommentApiController::class, 'index']);
Route::post('/requests/{request}/comments', [CommentApiController::class, 'store']);
Route::post('/requests/{bloodRequest}/donations', [DonationApiController::class, 'store']);
Route::post('/requests/{bloodRequest}/share', [DonationApiController::class, 'share']);
Route::patch('/donations/{donation}/complete', [DonationApiController::class, 'complete']);
Route::patch('/donations/{donation}/cancel', [DonationApiController::class, 'cancel']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('/requests/{request}/status', [RequestApiController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationApiController::class, 'markRead']);
});
