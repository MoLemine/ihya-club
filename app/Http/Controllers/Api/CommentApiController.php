<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\BloodRequest;
use App\Models\User;

class CommentApiController extends Controller
{
    public function index(BloodRequest $request)
    {
        return response()->json($request->comments()->latest()->paginate(20));
    }

    public function store(StoreCommentRequest $storeCommentRequest, BloodRequest $request)
    {
        $user = $storeCommentRequest->user()
            ?? User::query()->find($storeCommentRequest->session()->get('guest_user_id'));

        abort_if(! $user, 403, __('messages.comment_requires_identity'));

        $comment = $request->comments()->create([
            'user_id' => $user->id,
            'name' => $user->name,
            'content' => (string) $storeCommentRequest->input('content'),
        ]);

        return response()->json($comment, 201);
    }
}
