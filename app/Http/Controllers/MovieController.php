<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
	public function index(): \Illuminate\Database\Eloquent\Collection
	{
		return Movie::all();
	}
}
