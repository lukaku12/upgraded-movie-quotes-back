<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;

class QuoteController extends Controller
{
	public function show($slug, $id)
	{
		$quote = Quote::where('id', $id)->first();
		return response()->json($quote);
	}

	public function update($quote, $id, UpdateQuoteRequest $request)
	{
		$selected_quote = Quote::where('id', $id)->first();
		$data = [
			'title'  => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
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
}
