<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function open(Request $request, string $notification)
    {
        $notificationModel = $request->user()?->notifications()->whereKey($notification)->firstOrFail();
        $notificationModel->markAsRead();

        $bloodRequestId = $notificationModel->data['blood_request_id'] ?? null;

        if ($bloodRequestId && $request->user()?->isAdmin()) {
            return redirect()->route('admin.requests.index');
        }

        return redirect()->route('home');
    }

    public function markAllRead(Request $request)
    {
        $request->user()?->unreadNotifications->markAsRead();

        return back();
    }
}
