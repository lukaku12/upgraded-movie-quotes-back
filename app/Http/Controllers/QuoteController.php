<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
	public function index(): JsonResponse
	{
		$quotes =
			Quote::with(['movie', 'user', 'comments.user', 'likes'])
				->orderBy('created_at', 'DESC')
				->paginate(5);

		return response()->json($quotes, 200);
	}

	public function store(StoreQuoteRequest $request)
	{
		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);

		Quote::create($request->validated() + [
			'thumbnail' => $correctThumbnailPath,
		]);

		return response()->json('Quote Added successfully!', 200);
	}

	public function show(Quote $quote): JsonResponse
	{
		$quote->load(['comments', 'likes']);

		foreach ($quote->comments as $comment)
		{
			$commentAuthor = User::find($comment->user_id)->get(['username', 'picture']);
			$comment['username'] = $commentAuthor[0]->username;
			$comment['picture'] = $commentAuthor[0]->picture;
		}

		return response()->json($quote, 200);
	}

	public function update(Quote $quote, UpdateQuoteRequest $request): JsonResponse
	{
		$correctThumbnailPath = '';

		if ($request->thumbnail !== null)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		}

		$quote->update($request->validated() + [
			'thumbnail' => $correctThumbnailPath,
		]);

		return response()->json('Quote updated successfully!', 200);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		$quote->delete();
		return response()->json('Quote deleted successfully!', 200);
	}
}
