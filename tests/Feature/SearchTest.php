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

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->actingAs($user);
	}

	/* @test */
	public function test_user_cant_search_items_if_request_type_is_not_defined()
	{
		$response = $this->postJson(route('search'), [
			'type'  => '',
			'value' => '',
		]);

		$response->assertStatus(400);
	}

	/* @test */
	public function test_user_can_search_items_if_request_type_is_quote()
	{
		$quote = Quote::factory(4)->create(['title' => 'test']);
		Comment::create([
			'quote_id' => $quote[0]->id,
			'user_id'  => auth()->id(),
			'body'     => 'test',
		]);

		$response = $this->postJson(route('search'), [
			'type'  => 'quote',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_cant_search_items_if_request_type_is_movie_and_movie_has_quotes()
	{
		$movie = Movie::factory()->create(['title' => 'test']);
		Quote::factory(4)->create(['title' => 'test', 'movie_id' => $movie->id]);

		$response = $this->postJson(route('search'), [
			'type'  => 'movie',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_can_search_items_if_request_type_is_movie()
	{
		Movie::factory(4)->create(['title' => 'test']);

		$response = $this->postJson(route('search'), [
			'type'  => 'movie',
			'value' => 'test',
		]);

		$response->assertStatus(200);
	}
}
