<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AddMovieTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'    => $user->email,
			'password' => 'password',
		])->assertStatus(200);

		$this->actingAs($user);
	}

	/* @test */
	public function test_user_cant_add_movie_if_request_data_is_not_valid()
	{
		$response = $this->postJson(route('movies.store'), [
			'title_en'       => '',
			'title_ka'       => '',
			'director_en'    => '',
			'director_ka'    => '',
			'description_en' => '',
			'description_ka' => '',
			'thumbnail'      => '',
			'genres'         => '',
		]);

		$response
			->assertStatus(422)
			->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_can_add_movie_if_request_data_is_valid()
	{
		$this->withoutExceptionHandling();

		$genre1 = Genre::create(['name' => 'Genre 1']);
		$genre2 = Genre::create(['name' => 'Genre 2']);

		$response = $this->postJson(route('movies.store'), [
			'title_en'       => 'movie title',
			'title_ka'       => 'ფილმის სახელი',
			'director_en'    => 'director',
			'director_ka'    => 'დირექტორი',
			'description_en' => 'description',
			'description_ka' => 'აღწერა',
			'thumbnail'      => UploadedFile::fake()->create('thumbnail.jpg'),
			'genres'         => json_encode([$genre1->id, $genre2->id]),
		]);

		$response->assertStatus(200);
	}
}
