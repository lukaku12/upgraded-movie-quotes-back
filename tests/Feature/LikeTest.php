<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->actingAs($user);
	}

	/* @test */
	public function test_user_cant_like_post_if_request_data_doesnt_contain_quote_id()
	{
		$response = $this->postJson(route('like.store'), [
			'quote_id' => '',
		]);

		$response
			->assertStatus(422)
			->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_cant_like_post_if_user_already_has_post_liked()
	{
		$quote = Quote::factory()->create();
		Like::create(['user_id' => auth()->id(), 'quote_id' => $quote->id]);

		$response = $this->postJson(route('like.store'), [
			'quote_id' => $quote->id,
		]);

		$response->assertStatus(400);
	}

	/* @test */
	public function test_user_cant_like_post_if_request_data_is_valid()
	{
		$quote = Quote::factory()->create();

		$response = $this->postJson(route('like.store'), [
			'quote_id' => $quote->id,
		]);

		$response
			->assertStatus(200);
	}

	/* @test */
	public function test_user_can_unlike_post()
	{
		$quote = Quote::factory()->create();

		$response = $this->deleteJson(route('like.destroy', '1'), [
			'quote_id' => $quote->id,
		]);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function a_like_belongs_to_a_quote()
	{
		$user = User::factory()->create();
		$quote = Quote::factory()->create([
			'user_id' => $user->id,
		]);
		$like = Like::create([
			'user_id'  => $user->id,
			'quote_id' => $quote->id,
		]);

		$this->assertInstanceOf(Quote::class, $like->quote);
	}

	/** @test */
	public function a_like_belongs_to_a_user()
	{
		$user = User::factory()->create();
		$quote = Quote::factory()->create();
		$like = Like::create(['user_id' => $user->id, 'quote_id' => $quote->id]);

		$this->assertInstanceOf(User::class, $like->user);
	}
}
