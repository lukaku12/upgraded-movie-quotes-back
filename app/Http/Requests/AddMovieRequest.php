<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMovieRequest extends FormRequest
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
			'thumbnail'      => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'genres'         => 'required',
		];
	}
}
