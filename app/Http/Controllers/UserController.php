<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class UserController extends Controller
{
	public function index(): ?Authenticatable
	{
		return auth()->user();
	}

	public function update(UpdateUserRequest $request)
	{
		$data = [
			'username' => $request['username'],
		];
		// TODO - update user
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
