<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
	public function verify($user_id, Request $request): JsonResponse|RedirectResponse
	{
		if (!$request->hasValidSignature())
		{
			return response()->json(['msg' => 'Invalid/Expired url provided.'], 401);
		}

		$user = User::findOrFail($user_id);

		if (!$user->hasVerifiedEmail())
		{
			$user->markEmailAsVerified();
		}

		return redirect(env('FRONT_BASE_URL') . '/email-is-verified');
	}
}
