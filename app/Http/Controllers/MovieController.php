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

		return response()->json($movies);
	}

	public function show($slug): JsonResponse|Response
	{
		if (Movie::where('slug', $slug)->exists())
		{
			$movie = Movie::where('slug', $slug)->with(['quotes', 'genres'])->get()->first();

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

			return response()->json($movie);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
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

		foreach ($genres as $genre)
		{
			MovieGenre::create([
				'movie_id' => $movie->id,
				'genre_id' => $genre,
			]);
		}

		return response()->json($movie);
	}

	public function editMovie($slug): Response|JsonResponse
	{
		$movie = Movie::where('slug', $slug)->first();
		if ($movie)
		{
			return response()->json($movie);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}

	public function updateMovie(UpdateMovieRequest $request, $slug): Response|JsonResponse
	{
		$movie = Movie::where('slug', $slug)->first();

		$genres = MovieGenre::where('movie_id', $movie->id)->get();

		foreach ($genres as $genre)
		{
			$genre->delete();
		}

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

		if ($movie)
		{
			$movie->update($data);
			return response()->json($data);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}

	public function destroy($slug): Response|JsonResponse
	{
		$movie = Movie::where('slug', $slug)->first();

		if ($movie)
		{
			$movie->delete();
			return response(['error-msg' => 'Movie Deleted Successfully'], 200);
		}
		return response(['error' => true, 'error-msg' => 'Not found'], 404);
	}
}
