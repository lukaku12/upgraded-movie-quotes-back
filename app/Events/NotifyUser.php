<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyUser implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public array $data;

	public string $quote_author_id;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($data, $quote_author_id)
	{
		$this->data = $data;
		$this->quote_author_id = $quote_author_id;
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
		return new Channel('notify-user.' . $this->quote_author_id);
	}
}
