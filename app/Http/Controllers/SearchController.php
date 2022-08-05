<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Quote;

class SearchController extends Controller
{
	public function search(Request $request): JsonResponse
	{
		if ($request->type === 'quote')
		{
			$quotesCollection = Quote::filter(request(['value']))->get();
			foreach ($quotesCollection as $quote)
			{
				$quote = $this->getMovie_quote($quote);
				$quote['user'] = $quote->user()->get(['username', 'picture'])->first();
			}
			return response()->json($quotesCollection, 200);
		}
		if ($request->type === 'movie')
		{
			$movieQuotesCollections = Movie::filter(request(['value']))->with('quotes')->get()->pluck('quotes');

			$movieQuotes = [];
			foreach ($movieQuotesCollections as $movieQuotesCollection)
			{
				if (!$movieQuotesCollection->isEmpty())
				{
					$movieQuotes[] = $movieQuotesCollection[0];
					foreach ($movieQuotes as $movieQuote)
					{
						$movieQuote = $this->getMovie_quote($movieQuote);
						$movieQuote['user'] = $movieQuote->user()->get()->pluck(['username', 'picture']);
					}
				}
			}

			return response()->json($movieQuotes, 200);
		}
		return response()->json('Invalid search type', 400);
	}

	/**
	 * @param mixed $movieQuote
	 *
	 * @return mixed
	 */
	public function getMovie_quote(mixed $movieQuote): mixed
	{
		$movieQuote['likes'] = $movieQuote->likes;
		$movieQuote['comments'] = $movieQuote->comments;
		foreach ($movieQuote['comments'] as $comment)
		{
			$comment['username'] = $comment->user()->get()->pluck(['username'])[0];
			$comment['picture'] = $comment->user()->get()->pluck(['picture'])[0];
		}
		$movieQuote['movie'] = $movieQuote->movie;
		return $movieQuote;
	}
}
