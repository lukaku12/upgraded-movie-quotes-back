<?php

namespace App\Http\Controllers;

use App\Events\AddComment;
use App\Http\Requests\AddCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
	public function index(AddCommentRequest $request): JsonResponse
	{
		$comment = Comment::create($request->validated());

		$comment['username'] = $comment->user->username;
		$comment['picture'] = $comment->user->picture;
		$comment['body'] = $request->comment_body;
		unset($comment['user']);

		broadcast(
			(new AddComment([$comment]))->dontBroadcastToCurrentUser()
		);

		return response()->json('Comment Added successfully!', 200);
	}
}
