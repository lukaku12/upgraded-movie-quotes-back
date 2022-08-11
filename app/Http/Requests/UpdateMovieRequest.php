<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'title_en'       => 'required|string|max:255',
			'title_ka'       => 'required|string|max:255',
			'director_en'    => 'required|string|max:255',
			'director_ka'    => 'required|string|max:255',
			'description_en' => 'required|string|max:255',
			'description_ka' => 'required|string|max:255',
			'thumbnail'      => 'image',
			'genres'         => 'required',
			'slug'           => '',
			'user_id'        => '',
		];
	}

	public function prepareForValidation()
	{
		$this->merge([
			'slug'    => strtolower(str_replace('.', '', str_replace(' ', '-', $this->title_en))),
			'user_id' => auth()->id(),
		]);
	}
}
