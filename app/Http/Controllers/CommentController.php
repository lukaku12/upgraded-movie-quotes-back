<?php

namespace App\Http\Controllers;

use App\Events\AddComment;
use App\Http\Requests\AddCommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
	public function index(AddCommentRequest $request)
	{
		$data = [
			'quote_id'    => $request->quote_id,
			'body'        => $request->comment_body,
			'user_id'     => auth()->user()->id,
		];

		$savedData = Comment::create($data);

		$savedData['username'] = $savedData->user->username;
		$savedData['picture'] = $savedData->user->picture;
		$savedData['body'] = $request->comment_body;
		unset($savedData['user']);

		broadcast(
			(new AddComment([$savedData]))->dontBroadcastToCurrentUser()
		);

		return response()->json('Comment Added successfuly!', 200);
	}
}
