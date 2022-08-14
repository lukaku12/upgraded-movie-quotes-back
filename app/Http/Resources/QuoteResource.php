<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			'id'       => $this->id,
			'movie_id' => $this->movie_id,
			'user_id'  => $this->user_id,
			'title'    => [
				'en' => $this->title_en,
				'ka' => $this->title_ka,
			],
			'thumbnail'  => $this->thumbnail,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'movie'      => MovieResource::collection($this->movie),
			'user'       => UserResource::collection($this->user),
			'comments'   => CommentResource::collection($this->comments),
			'likes'      => LikeResource::collection($this->likes),
		];
	}
}
