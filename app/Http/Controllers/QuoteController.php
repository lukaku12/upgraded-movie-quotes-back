<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use App\Models\User;

class QuoteController extends Controller
{
	public function index()
	{
		$quotes = Quote::with(['movie', 'user', 'comments', 'likes'])->orderBy('created_at', 'DESC')->get();
		foreach ($quotes as $quote)
		{
			foreach ($quote->comments as $comment)
			{
				$commentAuthor = User::where('id', $comment->user_id)->get('username');
				$comment['username'] = $commentAuthor[0]->username;
			}
		}
		return response()->json($quotes, 200);
	}

	public function show($slug, $id)
	{
		$quote = Quote::where('id', $id)->first();
		return response()->json($quote);
	}

	public function addQuote($slug, AddQuoteRequest $request)
	{
		$data = [
			'title'  => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'movie_id'    => $request->movie_id,
			'user_id'     => auth()->user()->id,
		];

		Quote::create($data);

		return response()->json('Quote Added successfuly!', 200);
	}

	public function update($quote, $id, UpdateQuoteRequest $request)
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

	public function destroy($slug, $id)
	{
		$quote = Quote::where('id', $id)->first();
		$quote->delete();

		return response()->json('Quote deleted successfuly!', 200);
	}
}
