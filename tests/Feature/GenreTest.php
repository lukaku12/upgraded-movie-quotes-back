<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_user_can_get_all_genres()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('genres.get'));

		$response->assertStatus(200);
	}

	/** @test  */
	public function a_genre_belongs_to_many_movies()
	{
		$genre1 = Genre::factory()->create(['name' => 'Drama']);
		$movie = Movie::factory()->create();
		MovieGenre::create([
			'movie_id' => $movie->id,
			'genre_id' => $genre1->id,
		]);

		$this->assertInstanceOf(BelongsToMany::class, Genre::first()->movies());
	}
}
