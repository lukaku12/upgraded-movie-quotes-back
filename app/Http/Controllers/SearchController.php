<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Models\Quote;

class SearchController extends Controller
{
	public function search(Request $request)
	{
		if ($request->type === 'quote')
		{
			$quotes_collection = Quote::filter(request(['value']))->get();
			foreach ($quotes_collection as $quote)
			{
				$quote['likes'] = $quote->likes;
				$quote['comments'] = $quote->comments;
				$quote['movie'] = $quote->movie;
				$quote['user'] = $quote->user()->get()->pluck(['username', 'picture']);
			}
			return $quotes_collection;
		}
		if ($request->type === 'movie')
		{
			$movie_quotes_collections = Movie::filter(request(['value']))->with('quotes')->get()->pluck('quotes');

			$movie_quotes = [];
			foreach ($movie_quotes_collections as $movie_quotes_collection)
			{
				if (!$movie_quotes_collection->isEmpty())
				{
					$movie_quotes[] = $movie_quotes_collection[0];
					foreach ($movie_quotes as $movie_quote)
					{
						$movie_quote['likes'] = $movie_quote->likes;
						$movie_quote['comments'] = $movie_quote->comments;
						$movie_quote['movie'] = $movie_quote->movie;
						$movie_quote['user'] = $movie_quote->user()->get()->pluck(['username', 'picture']);
					}
				}
			}

			return response()->json($movie_quotes);
		}
		return response()->json('Invalid search type', 400);
	}
}
