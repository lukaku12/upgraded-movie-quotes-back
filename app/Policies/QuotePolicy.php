<?php

namespace App\Policies;

use App\Models\User;
use App\Models\quote;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class QuotePolicy
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
	 * @param \App\Models\quote $quote
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function view(User $user, quote $quote)
	{
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
	 * @param \App\Models\quote $quote
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function update(User $user, quote $quote)
	{
		if (!Gate::allows('view-quotes', $quote))
		{
			abort(403);
		}
	}

	/**
	 * Determine whether the user can delete the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\quote $quote
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function delete(User $user, quote $quote)
	{
		if (!Gate::allows('view-quotes', $quote))
		{
			abort(403);
		}
	}

	/**
	 * Determine whether the user can restore the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\quote $quote
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function restore(User $user, quote $quote)
	{
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 *
	 * @param \App\Models\User  $user
	 * @param \App\Models\quote $quote
	 *
	 * @return \Illuminate\Auth\Access\Response|bool
	 */
	public function forceDelete(User $user, quote $quote)
	{
	}
}
