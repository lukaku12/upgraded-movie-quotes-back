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
			$movie['thumbnail'] = $movie->quotes[0]->thumbnail;
		}
		return response()->json($movies);
	}

	public function show($slug)
	{
		if (Movie::where('slug', $slug)->exists())
		{
			$movie = Movie::where('slug', $slug)->first();
			return response()->json(['movie' => $movie, 'quotes' => $movie->quotes]);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}
}
