<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
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
			'titleEn'     => 'required|min:3|max:200|unique:movies,title',
			'titleKa'     => 'required|min:3|max:200|unique:movies,title',
			'movie_id'    => 'required',
			'thumbnail'   => 'required|image',
		];
	}
}
