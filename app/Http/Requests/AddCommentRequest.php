<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
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
			'quote_id'  => 'required',
			'body'      => 'required|max:300',
			'user_id'   => '',
		];
	}

	public function prepareForValidation()
	{
		$this->merge([
			'user_id' => auth()->id(),
		]);
	}
}
