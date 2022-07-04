<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
	public function index()
	{
		$movies = Movie::all();
		foreach ($movies as $movie)
		{
			$movie['number_of_quotes'] = count($movie->quotes);
			$movie['thumbnail'] = $movie->quotes[0]->thumbnail;
		}
		return $movies;
	}

	public function show($slug): array
	{
		$movie = Movie::where('slug', $slug)->first();
		return ['movie' => $movie, 'quotes' => $movie->quotes];
	}
}
