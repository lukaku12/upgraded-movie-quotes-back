<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
	public function verify(User $user, Request $request): JsonResponse|RedirectResponse
	{
		if (!$request->hasValidSignature())
		{
			return response()->json('Invalid/Expired url provided.', 401);
		}
		elseif (!$user->hasVerifiedEmail())
		{
			$user->markEmailAsVerified();
		}

		return redirect(env('FRONT_BASE_URL') . '/email-is-verified');
	}
}
