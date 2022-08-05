<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_can_get_movies()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('movies.get'));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_return_error_message_if_movie_slug_is_invalid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		Movie::factory()->create();

		$response = $this->getJson(route('movies.show', 'invalid-slug'));

		$response->assertStatus(404);
	}

	/* @test */
	public function test_return_movie_if_movie_slug_is_valid_and_movie_doesnt_belongs_to_user()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$movie = Movie::factory()->create();
		Quote::factory()->create(['movie_id' => $movie->id]);

		$response = $this->getJson(route('movies.show', $movie->slug));

		$response->assertStatus(403);
	}

	/* @test */
	public function test_return_movie_if_movie_slug_is_valid_and_movie_belongs_to_user()
	{
		$this->withoutExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$movie = Movie::factory()->create(['user_id' => $user->id]);
		Quote::factory()->create(['movie_id' => $movie->id]);

		$response = $this->getJson(route('movies.show', $movie->slug));

		$response->assertStatus(200);
	}
}
