<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test_comment_cant_be_added_if_request_data_is_not_provided()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$response = $this->postJson('/api/comment/add', [
			'quote_id'     => '',
			'comment_body' => '',
		]);

		$response->assertStatus(422)->assertJson(['errors' => true]);
	}

	public function test_comment_can_be_added_if_request_data_is_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$quote = Quote::factory()->create();

		$response = $this->postJson('/api/comment/add', [
			'quote_id'     => $quote->id,
			'comment_body' => 'This is a comment',
		]);

		$response->assertStatus(200);
	}
}