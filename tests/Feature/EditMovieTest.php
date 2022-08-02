<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EditMovieTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_cant_get_movie_data_if_movie_slug_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$genre1 = Genre::create(['name' => 'Genre 1']);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson('/api/movies/incorrect-movie-slug/edit');

		$response->assertStatus(404)->assertJson(['error' => true]);
	}

	/* @test */
	public function test_user_cant_get_movie_data_if_movie_slug_is_valid_but_movie_doesnt_belongs_to_user()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $other_user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson("/api/movies/$movie->slug/edit");

		$response->assertStatus(403);
	}

	/* @test */
	public function test_user_cant_get_movie_data_if_movie_slug_is_valid_and_movie_belongs_to_user()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson("/api/movies/$movie->slug/edit");

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_update_movie_data_if_request_data_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson("/api/movies/$movie->slug/edit", [
			'title'       => '',
			'genre_id'    => '',
			'year'        => '',
			'description' => '',
		]);

		$response->assertStatus(422)->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_cant_update_movie_if_movie_slug_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		Movie::factory()->create(['user_id' => $user->id]);
		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson('/api/movies/incorrect-movie-slug/edit', [
			'title_en'          => 'New title',
			'title_ka'          => 'ახალი სათაური',
			'description_en'    => 'New description',
			'description_ka'    => 'ახალი აღწერა',
			'director_en'       => 'New director',
			'director_ka'       => 'ახალი დირექტორი',
			'genres'            => json_encode([$genre1->id, $genre2->id]),
		]);

		$response->assertStatus(404)->assertJson(['error' => true]);
	}

	/* @test */
	public function test_user_cant_update_movie_if_movie_slug_is_valid_but_it_doesnt_belongs_to_user()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $other_user->id]);
		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson("/api/movies/$movie->slug/edit", [
			'title_en'          => 'New title',
			'title_ka'          => 'ახალი სათაური',
			'description_en'    => 'New description',
			'description_ka'    => 'ახალი აღწერა',
			'director_en'       => 'New director',
			'director_ka'       => 'ახალი დირექტორი',
			'genres'            => json_encode([$genre1->id, $genre2->id]),
		]);

		$response->assertStatus(403);
	}

	/* @test */
	public function test_user_can_update_movie_if_movie_slug_and_request_data_and_user_is_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);
		MovieGenre::create(['movie_id' => $movie->id, 'genre_id' => $genre1->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson("/api/movies/$movie->slug/edit", [
			'title_en'          => 'New title',
			'title_ka'          => 'ახალი სათაური',
			'description_en'    => 'New description',
			'description_ka'    => 'ახალი აღწერა',
			'director_en'       => 'New director',
			'director_ka'       => 'ახალი დირექტორი',
			'thumbnail'         => UploadedFile::fake()->image('thumbnail.jpg'),
			'genres'            => json_encode([$genre1->id, $genre2->id]),
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_delete_movie_if_slug_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson('/api/movies/invalid-slug/remove');

		$response->assertStatus(404)->assertJson(['error' => true]);
	}

	/* @test */
	public function test_user_cant_delete_movie_if_slug_is_valid_but_it_doesnt_belongs_to_user()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $other_user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson("/api/movies/$movie->slug/remove");

		$response->assertStatus(403);
	}

	/* @test */
	public function test_user_can_delete_movie_if_slug_is_valid_and_it_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson("/api/movies/$movie->slug/remove");

		$response->assertStatus(200);
	}
}
