<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class MovieResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param Request $request
	 *
	 * @return array|Arrayable|JsonSerializable
	 */
	public function toArray($request): array|JsonSerializable|Arrayable
	{
		return
			$this->resource->map(function ($movie) {
				return [
					'id'          => $movie->id,
					'title'       => $movie->getTranslations('title', ['en', 'ka']),
					'description' => $movie->description,
					'thumbnail'   => $movie->thumbnail,
					'slug'        => $movie->slug,
					'genres'      => $movie->genres->map(function ($genre) {
						return ['name' => $genre->name];
					}),
					'quotes' => $movie->quotes->map(function ($quote) {
						return [
							'id'       => $quote->id,
							'title'    => $quote->getTranslations('title', ['en', 'ka']),
							'likes'    => $quote->likes,
							'comments' => $quote->comments,
						];
					}),
					'created_at' => $movie->created_at,
					'updated_at' => $movie->updated_at,
				];
			});
	}
}
