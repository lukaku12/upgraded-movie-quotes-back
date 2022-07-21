<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveLike implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public array $data;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	public function broadcastWith(): array
	{
		return $this->data;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel
	 */
	public function broadcastOn(): Channel
	{
		return new Channel('remove-like');
	}
}
