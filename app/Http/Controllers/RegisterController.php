<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
	public function store(RegisterRequest $request)
	{
		if ($request->password !== $request->confirm_password)
		{
			throw ValidationException::withMessages([
				'password'        => 'Passwords do not match!',
			]);
		}

		$user = User::create([
			'username' => $request->username,
			'email'    => $request->email,
			'password' => bcrypt($request->password),
			'picture'  => 'profile-photo.png',
		]);

		auth()->login($user);

		event(new Registered($user));

		return response('successfull', 200);
	}
}
