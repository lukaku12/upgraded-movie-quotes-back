<?php

namespace Tests\Feature;

use App\Models\Comment;
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

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$response = $this->postJson(route('comment.store'), [
			'quote_id'     => '',
			'comment_body' => '',
		]);

		$response->assertStatus(422)->assertJson(['errors' => true]);
	}

	public function test_comment_can_be_added_if_request_data_is_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$quote = Quote::factory()->create();

		$response = $this->postJson(route('comment.store'), [
			'quote_id'     => $quote->id,
			'comment_body' => 'This is a comment',
		]);

		$response->assertStatus(200);
	}

	/** @test */
	public function a_comment_belongs_to_a_quote()
	{
		$user = User::factory()->create();
		$quote = Quote::factory()->create();
		$comment = Comment::create([
			'quote_id' => $quote->id,
			'user_id'  => $user->id,
			'body'     => 'This is a comment',
		]);
		$this->assertInstanceOf(Quote::class, $comment->quote);
	}
}
