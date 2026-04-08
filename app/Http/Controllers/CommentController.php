<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\BloodRequest;
use App\Models\User;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, BloodRequest $bloodRequest)
    {
        $user = $request->user() ?? User::query()->find($request->session()->get('guest_user_id'));

        abort_if(! $user, 403, __('messages.comment_requires_identity'));

        $bloodRequest->comments()->create([
            'user_id' => $user->id,
            'name' => $user->name,
            'content' => (string) $request->input('content'),
        ]);

        return back()->with('status', __('messages.comment_added'));
    }
}
