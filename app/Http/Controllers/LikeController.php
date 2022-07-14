<?php

namespace App\Http\Controllers;

use App\Models\Like;

class LikeController extends Controller
{
	public function index()
	{
		$request = request()->validate([
			'quote_id' => 'required',
		]);
		Like::create([
			'user_id'  => auth()->user()->id,
			'quote_id' => $request['quote_id'],
		]);

		return response()->json('Quote liked successfuly!', 200);
	}

	public function destroy()
	{
		$request = request()->validate([
			'quote_id' => 'required',
		]);
		$like = Like::where('quote_id', $request['quote_id'])->where('user_id', auth()->user()->id);

		$like->delete();

		return response()->json('Quote unliked successfuly!', 200);
	}
}
