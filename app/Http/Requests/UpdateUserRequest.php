<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
			'username'         => 'required|min:3|max:200|unique:users,username',
			'picture'          => 'image',
			'password'         => 'nullable|min:6|max:200',
			'confirm_password' => 'nullable|min:6|max:200|same:password',
		];
	}
}
