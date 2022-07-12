<?php

namespace App\Http\Controllers;

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

		Comment::create($data);

		return response()->json('Comment Added successfuly!', 200);
	}
}
