<?php

namespace App\Http\Controllers;

use App\Events\AddLike;
use App\Events\RemoveLike;
use App\Http\Requests\LikeRequest;
use App\Models\Like;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
	public function index(LikeRequest $request): JsonResponse
	{
		broadcast((new AddLike(['quote_id' => $request['quote_id'], 'user_id' => auth()->id()]))->dontBroadcastToCurrentUser());
		// check if user has already liked this quote
		if (Like::where('user_id', auth()->id())->where('quote_id', $request['quote_id'])->exists())
		{
			return response()->json('You have already liked this quote', 400);
		}
		Like::create([
			'user_id'  => auth()->user()->id,
			'quote_id' => $request['quote_id'],
		]);

		return response()->json('Quote liked successfully!', 200);
	}

	public function destroy(LikeRequest $request): JsonResponse
	{
		broadcast((new RemoveLike(['quote_id' => $request['quote_id'], 'user_id' => auth()->id()]))->dontBroadcastToCurrentUser());

		$like = Like::where('quote_id', $request['quote_id'])->where('user_id', auth()->user()->id);

		$like->delete();

		return response()->json('Quote unliked successfully!', 200);
	}
}
