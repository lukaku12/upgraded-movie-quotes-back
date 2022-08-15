<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MovieController extends Controller
{
	public function index()
	{
		$movies = Movie::where('user_id', auth()->id())->with('quotes')->get();

		return response()->json(new MovieResource($movies), 200);
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

		$data = [
			'title'       => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'director'    => [
				'en' => $request->director_en,
				'ka' => $request->director_ka,
			],
			'description' => [
				'en' => $request->description_en,
				'ka' => $request->description_ka,
			],
			'thumbnail'   => $correctThumbnailPath,
			'slug'        => $request->slug,
			'user_id'     => $request->user_id,
		];

		$movie = Movie::create($data);

		$genres = json_decode($request->genres);

		$movie->genres()->attach($genres);

		return response()->json($movie, 200);
	}

	public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
	{
		$movie->genres()->detach();

		$data = [
			'title'       => [
				'en' => $request->title_en,
				'ka' => $request->title_ka,
			],
			'director'    => [
				'en' => $request->director_en,
				'ka' => $request->director_ka,
			],
			'description' => [
				'en' => $request->description_en,
				'ka' => $request->description_ka,
			],
			'slug'        => $request->slug,
			'user_id'     => $request->user_id,
		];

		if ($request->thumbnail)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
			$data['thumbnail'] = $correctThumbnailPath;
		}

		$genres = json_decode($request->genres);

		$movie->genres()->attach($genres);

		$movie->update($data);
		return response()->json($movie, 200);
	}

	public function destroy(Movie $movie): JsonResponse
	{
		$movie->delete();
		return response()->json('Movie Deleted Successfully', 200);
	}
}
