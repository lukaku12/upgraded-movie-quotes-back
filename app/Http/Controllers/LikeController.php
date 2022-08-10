<?php

namespace App\Http\Controllers;

use App\Events\AddLike;
use App\Events\RemoveLike;
use App\Http\Requests\LikeRequest;
use App\Models\Like;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
	public function index(LikeRequest $request): JsonResponse
	{
		broadcast((new AddLike(['quote_id' => $request['quote_id'], 'user_id' => auth()->id()]))->dontBroadcastToCurrentUser());
		// check if user has already liked this quote
		if (Like::where([['user_id', auth()->id()], ['quote_id', $request['quote_id']]])->exists())
		{
			return response()->json('You have already liked this quote', 400);
		}
		Like::create([
			'user_id'  => auth()->user()->id,
			'quote_id' => $request['quote_id'],
		]);

		return response()->json('Quote liked successfully!', 200);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		broadcast((new RemoveLike(['quote_id' => $quote->id, 'user_id' => auth()->id()]))->dontBroadcastToCurrentUser());

		$like = Like::where('quote_id', $quote->id)->where('user_id', auth()->user()->id);

		$like->delete();

		return response()->json('Quote unliked successfully!', 200);
	}
}
