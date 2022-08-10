<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

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

	public function store(StoreQuoteRequest $request): JsonResponse
	{
		$data = [
			'title'  => [
				'en' => $request['title_en'],
				'ka' => $request['title_ka'],
			],
			'user_id'     => auth()->id(),
			'movie_id'    => $request['movie_id'],
		];

		// upload thumbnail
		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		$data['thumbnail'] = $correctThumbnailPath;

		Quote::create($data);

		return response()->json('Quote Added successfully!', 200);
	}

	public function show($slug, Quote $quote): JsonResponse
	{
		if (!Gate::allows('view-quotes', $quote))
		{
			abort(403);
		}
		$quote->load(['comments', 'likes']);

		foreach ($quote->comments as $comment)
		{
			$commentAuthor = User::find($comment->user_id)->get(['username', 'picture']);
			$comment['username'] = $commentAuthor[0]->username;
			$comment['picture'] = $commentAuthor[0]->picture;
		}

		return response()->json($quote, 200);
	}

	public function update($slug, Quote $quote, UpdateQuoteRequest $request): JsonResponse
	{
		if (!Gate::allows('view-quotes', $quote))
		{
			abort(403);
		}
		$data = [
			'title'  => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'user_id'     => auth()->user()->id,
			'movie_id'    => $request->movie_id,
		];
		if ($request->thumbnail !== null)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
			$data['thumbnail'] = $correctThumbnailPath;
		}
		$quote->update($data);

		return response()->json('Quote updated successfully!', 200);
	}

	public function destroy($slug, Quote $quote): JsonResponse
	{
		if (!Gate::allows('view-quotes', $quote))
		{
			abort(403);
		}
		$quote->delete();
		return response()->json('Quote deleted successfully!', 200);
	}
}
