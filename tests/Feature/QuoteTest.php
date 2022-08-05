<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class QuoteTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_all_quotes_can_be_received()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->count(3)->create();
		Comment::create(['user_id' => $user->id, 'quote_id' => $quote[0]->id, 'body' => 'test comment']);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('quotes.get'));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_quote_cant_be_added_if_request_data_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.store'), [
			'title_en'     => '',
			'title_ka'     => '',
			'movie_id'     => '',
			'thumbnail'    => '',
		]);

		$response->assertStatus(422)->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_quote_can_be_added_if_request_data_is_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$quote = Quote::factory()->create(['movie_id' => $movie->id]);
		Comment::create(['user_id' => $user->id, 'quote_id' => $quote->id, 'body' => 'test comment']);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.store'), [
			'title_en'     => 'new title',
			'title_ka'     => 'ახალი სათაური',
			'movie_id'     => $movie->id,
			'thumbnail'    => UploadedFile::fake()->create('thumbnail.jpg'),
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_quote_cant_be_returned_if_quote_id_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('quotes.show', [$movie->slug, '199']));

		$response->assertStatus(404);
	}

	/* @test */
	public function test_quote_cant_be_returned_if_quote_id_is_valid_but_it_doesnt_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $other_user->id]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('quotes.show', [$movie->slug, $quote->id]));

		$response->assertStatus(403);
	}

	/* @test */
	public function test_quote_can_be_returned_if_quote_id_is_valid_and_it_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $user->id]);
		Comment::create(['user_id' => $user->id, 'quote_id' => $quote->id, 'body' => 'test comment']);
		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('quotes.show', [$movie->slug, $quote->id]));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_delete_quote_if_quote_doest_exists()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.destroy', [$movie->slug, '199']));

		$response->assertStatus(404);
	}

	/* @test */
	public function test_user_cant_delete_quote_if_quote_exists_but_it_doesnt_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $other_user->id]);
		$movie = Movie::factory()->create(['user_id' => $other_user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->deleteJson(route('quotes.destroy', [$movie->slug, $quote->id]));

		$response->assertStatus(403);
	}

	/* @test */
	public function test_user_can_delete_quote_if_quote_exists_and_it_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$quote = Quote::factory()->create(['user_id' => $user->id, 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->deleteJson(route('quotes.destroy', [$movie->slug, $quote->id]));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_update_quote_request_data_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$quote = Quote::factory()->create(['user_id' => $user->id, 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.update', [$movie->slug, $quote->id]), [
			'title_en'      => '',
			'title_ka'      => '',
			'movie_id'      => '',
			'thumbnail'     => '',
		]);

		$response->assertStatus(422);
	}

	/* @test */
	public function test_user_cant_update_quote_request_data_is_valid_but_quote_is_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$quote = Quote::factory()->create(['user_id' => $user->id, 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.update', [$movie->slug, '32']), [
			'title_en'      => 'asdasdasd',
			'title_ka'      => 'ასდასდასდ',
			'movie_id'      => $quote->movie_id,
			'thumbnail'     => UploadedFile::fake()->create('thumbnail.jpg'),
		]);

		$response->assertStatus(404);
	}

	/* @test */
	public function test_user_cant_update_quote_request_data_is_valid_and_quote_is_valid_but_it_doesnt_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $other_user->id]);
		$quote = Quote::factory()->create(['user_id' => $other_user->id, 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.update', [$movie->slug, $quote->id]), [
			'title_en'      => 'asdasdasd',
			'title_ka'      => 'ასდასდასდ',
			'movie_id'      => $quote->movie_id,
			'thumbnail'     => UploadedFile::fake()->create('thumbnail.jpg'),
		]);

		$response->assertStatus(403);
	}

	/* @test */
	public function test_user_can_update_quote_request_data_is_valid_and_quote_is_valid_and_it_belongs_to_user()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['user_id' => $user->id]);
		$quote = Quote::factory()->create(['user_id' => $user->id, 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('quotes.update', [$movie->slug, $quote->id]), [
			'title_en'      => 'asdasdasd',
			'title_ka'      => 'ასდასდასდ',
			'movie_id'      => $quote->movie_id,
			'thumbnail'     => UploadedFile::fake()->create('thumbnail.jpg'),
		]);

		$response->assertStatus(200);
	}
}
