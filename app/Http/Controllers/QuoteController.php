<?php

namespace App\Http\Controllers;

use App\Models\Quote;

class QuoteController extends Controller
{
	public function show($slug, $id)
	{
		$quote = Quote::where('id', $id)->first();
		return response()->json($quote);
	}
}
