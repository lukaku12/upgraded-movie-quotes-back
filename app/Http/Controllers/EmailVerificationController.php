<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
	public function show()
	{
		if (auth()->user()->hasVerifiedEmail())
		{
			return redirect('/');
		}
		return view('session.verify-email');
	}

	public function request()
	{
		auth()->user()->sendEmailVerificationNotification();

		return back();
	}

	public function verify(EmailVerificationRequest $request)
	{
		$request->fulfill();

		return redirect('/');
	}
}
