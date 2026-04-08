<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->notifications()->paginate(20));
    }

    public function markRead(Request $request, string $notification)
    {
        $request->user()->notifications()->whereKey($notification)->update(['read_at' => now()]);

        return response()->json(['status' => 'read']);
    }
}
