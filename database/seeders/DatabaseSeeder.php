<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$user = User::factory()->create([
			'username'     => 'luka',
			'email'        => 'lukakurdadze2@gmail.com',
			'password'     => bcrypt('password'),
		]);

		Movie::factory(10)->create([
			'user_id' => $user->id,
		]);

		Quote::factory(10)->create([
			'user_id' => $user->id,
		]);
		Comment::factory(10)->create([
			'user_id' => $user->id,
		]);
	}
}
