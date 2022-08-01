<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class QuoteController extends Controller
{
	public function index(): Response|JsonResponse
	{
		$quotes = Quote::with(['movie', 'user', 'comments', 'likes'])->orderBy('created_at', 'DESC')->paginate(5);
		if ($quotes)
		{
			foreach ($quotes as $quote)
			{
				foreach ($quote->comments as $comment)
				{
					$commentAuthor = User::where('id', $comment->user_id)->get(['username', 'picture']);
					$comment['username'] = $commentAuthor[0]->username;
					$comment['picture'] = $commentAuthor[0]->picture;
				}
			}
			return response()->json($quotes, 200);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}

	public function store(): JsonResponse
	{
		$request = request()->validate([
			'title_en'     => 'required|min:3|max:200|unique:movies,title',
			'title_ka'     => 'required|min:3|max:200|unique:movies,title',
			'thumbnail'    => 'required|image',
			'movie_id'     => 'required',
		]);
		$data = [
			'title'  => [
				'en' => $request['title_en'],
				'ka' => $request['title_ka'],
			],
			'user_id'     => auth()->id(),
			'movie_id'    => $request['movie_id'],
		];

		// upload thumbnail
		$thumbnailPath = request()->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		$data['thumbnail'] = $correctThumbnailPath;

		Quote::create($data);

		return response()->json('Quote Added successfuly!', 200);
	}

	public function show($slug, $id): Response|JsonResponse
	{
		$quote = Quote::where('id', $id)->with(['comments', 'likes'])->first();

		if ($quote)
		{
			if (!Gate::allows('view-quotes', $quote))
			{
				abort(403);
			}

			foreach ($quote->comments as $comment)
			{
				$commentAuthor = User::where('id', $comment->user_id)->get(['username', 'picture']);
				$comment['username'] = $commentAuthor[0]->username;
				$comment['picture'] = $commentAuthor[0]->picture;
			}

			return response()->json($quote);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}

	public function addQuoteForMovie(AddQuoteRequest $request): JsonResponse
	{
		$data = [
			'title'  => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'movie_id'    => $request->movie_id,
			'user_id'     => auth()->user()->id,
		];

		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		$data['thumbnail'] = $correctThumbnailPath;

		Quote::create($data);

		return response()->json('Quote Added successfuly!', 200);
	}

	public function update($quote, $id, UpdateQuoteRequest $request): JsonResponse
	{
		$selected_quote = Quote::where('id', $id)->first();
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
		$selected_quote->update($data);

		return response()->json('Quote updated successfuly!', 200);
	}

	public function destroy($slug, $id): JsonResponse
	{
		$quote = Quote::where('id', $id)->first();
		$quote->delete();

		return response()->json('Quote deleted successfuly!', 200);
	}
}
