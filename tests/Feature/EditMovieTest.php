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

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->actingAs($user);
	}

	/* @test */
	public function test_user_cant_get_movie_data_if_movie_slug_is_not_valid()
	{
		Genre::create(['name' => 'Genre 1']);

		$response = $this->getJson('/api/movies/incorrect-movie-slug/edit');

		$response->assertStatus(404);
	}

	/* @test */
	public function test_user_cant_get_movie_data_if_movie_slug_is_valid_and_movie_belongs_to_user()
	{
		$this->withExceptionHandling();

		$movie = Movie::factory()->create(['user_id' => auth()->id()]);

		$response = $this->getJson(route('movies.show', $movie->slug));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_update_movie_data_if_request_data_is_not_valid()
	{
		$movie = Movie::factory()->create(['user_id' => auth()->id()]);

		$response = $this->postJson(route('movies.update', $movie->slug), [
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
		Movie::factory()->create(['user_id' => auth()->id()]);
		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);

		$response = $this->postJson(route('movies.update', 'incorrect-slug'), [
			'title_en'          => 'New title',
			'title_ka'          => 'ახალი სათაური',
			'description_en'    => 'New description',
			'description_ka'    => 'ახალი აღწერა',
			'director_en'       => 'New director',
			'director_ka'       => 'ახალი დირექტორი',
			'genres'            => json_encode([$genre1->id, $genre2->id]),
		]);

		$response->assertStatus(404);
	}

	/* @test */
	public function test_user_can_update_movie_if_movie_slug_and_request_data_and_user_is_valid()
	{
		$movie = Movie::factory()->create(['user_id' => auth()->id()]);
		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);
		MovieGenre::create(['movie_id' => $movie->id, 'genre_id' => $genre1->id]);

		$response = $this->postJson(route('movies.update', $movie->slug), [
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
		Movie::factory()->create(['user_id' => auth()->id()]);

		$response = $this->deleteJson(route('movies.destroy', 'incorrect-slug'));

		$response->assertStatus(404);
	}

	/* @test */
	public function test_user_can_delete_movie_if_slug_is_valid_and_it_belongs_to_user()
	{
		$movie = Movie::factory()->create(['user_id' => auth()->id()]);

		$response = $this->deleteJson(route('movies.destroy', $movie->slug));

		$response->assertStatus(200);
	}
}
