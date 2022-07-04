<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Quote;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;

class QuoteController extends Controller
{
	public function store(StoreQuoteRequest $request): \Illuminate\Contracts\Foundation\Application|ResponseFactory|\Illuminate\Http\Response
	{
		$data = [
			'title'  => [
				'en' => $request->titleEn,
				'ka' => $request->titleEn,
			],
			'movie_id'    => $request->movie_id,
		];
		$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
		$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
		$data['thumbnail'] = $correctThumbnailPath;

		Quote::create($data);

		return response('successfull', 200);
	}

	public function update(Quote $quote, UpdateQuoteRequest $request): RedirectResponse
	{
		$data = [
			'title'  => [
				'en' => $request->titleEn,
				'ka' => $request->titleEn,
			],
			'movie_id'    => $request->movie_id,
		];
		if ($request->thumbnail !== null)
		{
			$thumbnailPath = $request->file('thumbnail')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
			$data['thumbnail'] = $correctThumbnailPath;
		}
		$quote->update($data);

		return redirect('admin/quotes')->with('success', __('ui.quote_has_been_updated'));
	}

	public function destroy(Quote $quote): RedirectResponse
	{
		$quote->delete();

		return back()->with('success', __('ui.quote_has_been_deleted'));
	}
}
