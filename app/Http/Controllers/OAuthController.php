<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
	public function redirect()
	{
		return Socialite::driver('google')->redirect();
	}

	public function callback()
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'                 => $googleUser->name,
			'email'                    => $googleUser->email,
			'google_token'             => $googleUser->token,
			'google_refresh_token'     => $googleUser->refreshToken,
		]);

		Auth::login($user);

		return redirect('http://localhost:3000/');
	}
}
