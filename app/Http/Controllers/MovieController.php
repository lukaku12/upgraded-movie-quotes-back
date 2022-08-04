<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class MovieController extends Controller
{
	public function index(): JsonResponse
	{
		$movies = Movie::where('user_id', auth()->id())->with('quotes')->get();

		return response()->json($movies, 200);
	}

	public function show(Movie $movie): JsonResponse|Response
	{
		$movie->with(['quotes', 'genres'])->get()->first();

		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}

		// get likes for each quote
		foreach ($movie->quotes as $quote)
		{
			$quote->likes = $quote->likes()->get();
			$quote->comments = $quote->comments()->get();
		}

		return response()->json($movie, 200);
	}

	public function store(AddMovieRequest $request): JsonResponse
	{
		$data = [
			'user_id'   => auth()->id(),
			'slug'      => strtolower(str_replace('.', '', str_replace(' ', '-', $request->title_en))),
			'title'     => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'director' => [
				'en' => $request->director_en,
				'ka' => $request->director_ka,
			],
			'description' => [
				'en' => $request->description_en,
				'ka' => $request->description_ka,
			],
		];

		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		$data['thumbnail'] = $correctThumbnailPath;

		$movie = Movie::create($data);

		$genres = json_decode($request->genres);

		$movie->genres()->attach($genres);

		return response()->json($movie, 200);
	}

	public function edit(Movie $movie): Response|JsonResponse
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}
		return response()->json($movie, 200);
	}

	public function update(UpdateMovieRequest $request, Movie $movie): Response|JsonResponse
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}

		$movie->genres()->detach();

		$data = [
			'user_id'   => auth()->id(),
			'slug'      => strtolower(str_replace('.', '', str_replace(' ', '-', $request->title_en))),
			'title'     => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'director' => [
				'en' => $request->director_en,
				'ka' => $request->director_ka,
			],
			'description' => [
				'en' => $request->description_en,
				'ka' => $request->description_ka,
			],
		];
		if ($request->thumbnail)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
			$data['thumbnail'] = $correctThumbnailPath;
		}

		$genres = json_decode($request->genres);

		foreach ($genres as $genre)
		{
			MovieGenre::create([
				'movie_id' => $movie->id,
				'genre_id' => $genre,
			]);
		}

		$movie->update($data);
		return response()->json($data, 200);
	}

	public function destroy(Movie $movie): Response|JsonResponse
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}
		$movie->delete();
		return response()->json('Movie Deleted Successfully', 200);
	}
}
