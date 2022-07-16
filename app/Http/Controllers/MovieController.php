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
			return response()->json($movie);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}
}
