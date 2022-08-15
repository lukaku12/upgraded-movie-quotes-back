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

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->actingAs($user);
	}

	/* @test */
	public function test_user_can_get_movies()
	{
		$response = $this->getJson(route('movies.get'));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_return_error_message_if_movie_slug_is_invalid()
	{
		Movie::factory()->create();

		$response = $this->getJson(route('movies.show', 'invalid-slug'));

		$response->assertStatus(404);
	}

	/* @test */
	public function test_return_movie_if_movie_slug_is_valid_and_movie_belongs_to_user()
	{
		$this->withoutExceptionHandling();

		$movie = Movie::factory()->create(['user_id' => auth()->id()]);
		Quote::factory()->create(['movie_id' => $movie->id]);

		$response = $this->getJson(route('movies.show', $movie->slug));

		$response->assertStatus(200);
	}
}
