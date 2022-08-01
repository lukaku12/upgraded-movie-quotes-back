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

	/* @test */
	public function test_user_cant_like_post_if_request_data_doesnt_contain_quote_id()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson('/api/like/add', [
			'quote_id' => '',
		]);

		$response
			->assertStatus(422)
			->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_cant_like_post_if_user_already_has_post_liked()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$quote = Quote::factory()->create();
		$like = Like::create(['user_id' => $user->id, 'quote_id' => $quote->id]);

		$response = $this->postJson('/api/like/add', [
			'quote_id' => $quote->id,
		]);

		$response
			->assertStatus(400)
			->assertJson(['error-msg' => 'You have already liked this quote']);
	}

	/* @test */
	public function test_user_cant_like_post_if_request_data_is_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$quote = Quote::factory()->create();

		$response = $this->postJson('/api/like/add', [
			'quote_id' => $quote->id,
		]);

		$response
			->assertStatus(200);
	}

	/* @test */
	public function test_user_can_unlike_post()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$quote = Quote::factory()->create();

		$response = $this->postJson('/api/like/remove', [
			'quote_id' => $quote->id,
		]);

		$response
			->assertStatus(200);
	}
}
