<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	/**
	 * Register user with given credentials
	 */
	public function register(RegisterRequest $request): JsonResponse
	{
		$user = User::create([
			'username'     => $request->username,
			'email'        => $request->email,
			'password'     => Hash::make($request->password),
		]);

		auth()->attempt($request->all());

		$user->sendEmailVerificationNotification();

		return response()->json('User successfully registered!', 200);
	}

	/**
	 * Get a JWT via given credentials.
	 */
	public function login(AuthRequest $request): JsonResponse
	{
		$token = auth()->attempt($request->all());

		if (!$token)
		{
			return response()->json('User Does not exist!', 401);
		}
		if (!auth()->user()->hasVerifiedEmail())
		{
			auth()->user()->sendEmailVerificationNotification();
			return response()->json('Please verify your email first!', 401);
		}
		return $this->respondWithToken($token);
	}

	/**
	 * Log the user out (Invalidate the token).
	 */
	public function logout(): JsonResponse
	{
		auth()->logout();

		return response()->json('Successfully logged out');
	}

	/**
	 * Get the token array structure.
	 */
	public function respondWithToken(string $token): JsonResponse
	{
		return response()->json([
			'access_token' => $token,
			'token_type'   => 'bearer',
			'expires_in'   => auth()->factory()->getTTL() * 60,
		]);
	}
}
