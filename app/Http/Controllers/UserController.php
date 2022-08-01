<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
	public function index(): ?Authenticatable
	{
		return auth()->user();
	}

	public function update(UpdateUserRequest $request): JsonResponse
	{
		$data = [];
		if ($request->username)
		{
			$data['username'] = $request->username;
		}
		if ($request->picture !== null)
		{
			$thumbnailPath = $request->file('picture')->store('thumbnails');
			$correctThumbnailPath = str_replace('thumbnails/', '', $thumbnailPath);
			$data['picture'] = $correctThumbnailPath;
		}
		if ($request->password !== null && $request->confirm_password !== null)
		{
			$data['password'] = bcrypt($request['password']);
		}

		$user = User::where('id', auth()->id())->first();

		$user->update($data);

		return response()->json('User updated successfully', 200);
	}
}
