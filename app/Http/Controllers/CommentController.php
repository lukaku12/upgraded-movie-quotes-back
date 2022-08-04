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
		$data = [
			'quote_id'    => $request->quote_id,
			'body'        => $request->comment_body,
			'user_id'     => auth()->user()->id,
		];

		$comment = Comment::create($data);

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
