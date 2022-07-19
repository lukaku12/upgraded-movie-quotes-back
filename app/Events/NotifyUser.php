<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
	 * @return PrivateChannel
	 */
	public function broadcastOn(): PrivateChannel
	{
		return new PrivateChannel('notify-user.' . $this->quote_author_id);
	}
}
