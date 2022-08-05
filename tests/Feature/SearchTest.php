<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_cant_search_items_if_request_type_is_not_defined()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('search'), [
			'type'  => '',
			'value' => '',
		]);

		$response->assertStatus(400);
	}

	/* @test */
	public function test_user_can_search_items_if_request_type_is_quote()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory(4)->create(['title' => 'test']);
		Comment::create([
			'quote_id' => $quote[0]->id,
			'user_id'  => $user->id,
			'body'     => 'test',
		]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('search'), [
			'type'  => 'quote',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_search_items_if_request_type_is_movie_and_movie_has_quotes()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$movie = Movie::factory()->create(['title' => 'test']);
		Quote::factory(4)->create(['title' => 'test', 'movie_id' => $movie->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('search'), [
			'type'  => 'movie',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_can_search_items_if_request_type_is_movie()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		Movie::factory(4)->create(['title' => 'test']);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('search'), [
			'type'  => 'movie',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}
}
