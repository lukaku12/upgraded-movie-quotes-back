<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
	public function redirect(): RedirectResponse
	{
		return Socialite::driver('google')->redirect();
	}

	public function callback(): RedirectResponse
	{
		$googleUser = Socialite::driver('google')->stateless()->user();

		$googlePassword = Hash::make($googleUser->id);

		$user = User::where('email', $googleUser->email)->first();

		if (!$user || ($user->google_id))
		{
			User::updateOrCreate([
				'google_id' => $googleUser->id,
			], [
				'username'                 => $googleUser->name,
				'email'                    => $googleUser->email,
				'google_token'             => $googleUser->token,
				'google_refresh_token'     => $googleUser->refreshToken,
				'password'                 => $googlePassword,
			]);

			$token = auth()->attempt(['email' => $googleUser->email, 'password' => $googleUser->id]);

			return redirect(env('FRONT_BASE_URL') . '/oauth?token=' . $token . '&type=bearer&expires_in=' . auth()->factory()->getTTL() * 60);
		}
		return redirect(env('FRONT_BASE_URL') . '/register?error=409');
	}
}
