<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Genre;
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
		Genre::factory()->create(['name' => 'Drama']);
		Genre::factory()->create(['name' => 'Comedy']);
		Genre::factory()->create(['name' => 'Action']);
		Genre::factory()->create(['name' => 'Horror']);
		Genre::factory()->create(['name' => 'Thriller']);
		Genre::factory()->create(['name' => 'Sci-Fi']);
		Genre::factory()->create(['name' => 'Mystery']);
		Genre::factory()->create(['name' => 'Fantasy']);
		Genre::factory()->create(['name' => 'Romance']);
		Genre::factory()->create(['name' => 'History']);
		Genre::factory()->create(['name' => 'War']);
		Genre::factory()->create(['name' => 'Western']);
		Genre::factory()->create(['name' => 'Animation']);
		Genre::factory()->create(['name' => 'Family']);
		Genre::factory()->create(['name' => 'Musical']);
		Genre::factory()->create(['name' => 'Documentary']);
	}
}
