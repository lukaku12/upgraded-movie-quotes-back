<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Http\Requests\NotificationRequest;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
	public function index(NotificationRequest $request): JsonResponse
	{
		$quote_user_id = Quote::where('id', $request['quote_id'])->with('user')->pluck('user_id')->first();

		// don't save notification if user is the same as the quote user
		if ($request['user_id'] == $quote_user_id)
		{
			return response()->json('You cannot notify yourself', 200);
		}

		$message = Notification::create($request->validated());
		$message['username'] = $message->user->username;
		$message['quote_author_id'] = $message->quote->user->id;

		unset($message['user'], $message['quote']);

		broadcast(
			(new NotifyUser([$message], $message['quote_author_id']))->dontBroadcastToCurrentUser()
		);
		return response()->json('Notification Sent successfully', 200);
	}

	public function updateNotifications(): JsonResponse
	{
		$notifications = $this->getNotifications();

		foreach ($notifications as $notification)
		{
			unset($notification['username']);
			$notification->update([
				'read_at' => now(),
			]);
		}

		return response()->json($notifications, 200);
	}

	public function getUserNotifications(): JsonResponse
	{
		$notifications = $this->getNotifications();

		return response()->json($notifications, 200);
	}

	/**
	 * @return mixed
	 */
	public function getNotifications(): mixed
	{
		$user_notifications = Quote::where('user_id', auth()->user()->id)->with(['notifications'])->get();
		$user_notifications = $user_notifications->map(function ($item) {
			$item['notifications'] = $item->notifications->map(function ($item) {
				$item['username'] = $item->user->username;
				$item['user_id'] = $item->user->id;
				unset($item['user']);
				return $item;
			});
			return $item;
		});
		return $user_notifications->map(function ($item) {
			return $item->notifications;
		})->flatten();
	}
}
