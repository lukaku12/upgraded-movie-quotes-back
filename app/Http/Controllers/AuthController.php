<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	public function store(AuthRequest $request)
	{
		if (!auth()->attempt(
			['email' => $request->email, 'password' => $request->password],
			$request->has('remember_device')
		))
		{
			throw ValidationException::withMessages([
				'password' => 'Your provided credentials could not be verified',
			]);
		}
		session()->regenerate();

		return response('successfull', 200);
	}
}
