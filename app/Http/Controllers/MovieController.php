<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
	public function index()
	{
		$movies = Movie::where('user_id', auth()->id())->with('quotes')->get();

		return response()->json($movies);
	}

	public function show($slug)
	{
		if (Movie::where('slug', $slug)->exists())
		{
			$movie = Movie::where('slug', $slug)->with('quotes')->get()->first();
			// get likes for each quote
			foreach ($movie->quotes as $quote)
			{
				$quote->likes = $quote->likes()->get();
				$quote->comments = $quote->comments()->get();
			}

			return response()->json($movie);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}
}
