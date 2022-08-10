<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SubmitPasswordResetRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
	public function submitForgetPasswordForm(PasswordResetRequest $request): JsonResponse
	{
		$token = Str::random(64);

		DB::table('password_resets')->insert([
			'email'      => $request->email,
			'token'      => $token,
			'created_at' => Carbon::now(),
		]);

		Mail::send('email.forgetPassword', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
			$message->to($request->email);
			$message->subject('Reset Password');
		});

		return response()->json('Email sent successfully', 200);
	}

	public function submitResetPasswordForm(SubmitPasswordResetRequest $request): JsonResponse
	{
		$updatePassword = DB::table('password_resets')
			->firstWhere([
				'email' => $request->email,
				'token' => $request->token,
			]);

		if (!$updatePassword)
		{
			return response()->json('Invalid token', 400);
		}

		User::where('email', $request->email)
			->update(['password' => bcrypt($request->password)]);

		DB::table('password_resets')->where(['email'=> $request->email])->delete();

		return response()->json('Password updated successfully', 200);
	}
}
