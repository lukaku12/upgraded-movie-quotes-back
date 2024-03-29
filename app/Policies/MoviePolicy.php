<?php

namespace App\Policies;

use App\Models\User;
use App\Models\movie;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class MoviePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any models.
	 *
	 * @param \App\Models\User $user
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function viewAny(User $user)
	{
	}

	/**
	 * Determine whether the user can view the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\movie $movie
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function view(User $user, movie $movie)
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}
	}

	/**
	 * Determine whether the user can create models.
	 *
	 * @param \App\Models\User $user
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function create(User $user)
	{
	}

	/**
	 * Determine whether the user can update the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\movie $movie
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function update(User $user, movie $movie)
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\movie $movie
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function delete(User $user, movie $movie)
	{
		if (!Gate::allows('view-movie', $movie))
		{
			abort(403);
		}
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\movie $movie
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function restore(User $user, movie $movie)
	{
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\movie $movie
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function forceDelete(User $user, movie $movie)
	{
	}
}
