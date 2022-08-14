<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MovieController extends Controller
{
	public function index()
	{
		$movies = Movie::where('user_id', auth()->id())->with('quotes')->get();

		return response()->json($movies, 200);
	}

	public function show(Movie $movie): JsonResponse|Response
	{
		$movie->with(['quotes', 'genres'])->get()->first();
		$movie['quotes'] = $movie->quotes()->with(['likes', 'comments'])->get();
		$movie->load('genres');

		return response()->json($movie, 200);
	}

	public function store(AddMovieRequest $request): JsonResponse
	{
		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);

		$movie = Movie::create([$request->validated(), 'thumbnail' => $correctThumbnailPath]);

		$genres = json_decode($request->genres);

		$movie->genres()->attach($genres);

		return response()->json($movie, 200);
	}

	public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
	{
		$movie->genres()->detach();

		$correctThumbnailPath = '';
		if ($request->thumbnail)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		}

		$genres = json_decode($request->genres);

		$movie->genres()->attach($genres);

		$movie->update([$request->validated(), 'thumbnail' => $correctThumbnailPath]);
		return response()->json($movie, 200);
	}

	public function destroy(Movie $movie): JsonResponse
	{
		$movie->delete();
		return response()->json('Movie Deleted Successfully', 200);
	}
}
