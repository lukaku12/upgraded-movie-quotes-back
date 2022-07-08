<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;

class UserController extends Controller
{
	public function index(): ?Authenticatable
	{
		return auth()->user();
	}
}
