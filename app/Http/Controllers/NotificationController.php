<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Models\Notification;
use App\Models\Quote;

class NotificationController extends Controller
{
	public function index()
	{
		$request = request()->all();

//		if ($request['message'] === 'Reacted to your quote')
//		{
//			if (Notification::where('user_id', $request['user_id'])->where('quote_id', $request['quote_id'])->exists())
//			{
//				return response()->json([
//					'message' => 'Notification already exists',
//					'status'  => 'error',
//				], 400);
//			}
//		}

		$data = [
			'user_id'  => $request['user_id'],
			'quote_id' => $request['quote_id'],
			'message'  => $request['message'],
			'read_at'  => $request['read_at'],
		];

		$message = Notification::create($data);
		$message['username'] = $message->user->username;
		$message['quote_author_id'] = $message->quote->user->id;

		unset($message['user'], $message['quote']);

		broadcast((new NotifyUser([$message], $message['quote_author_id'])));
		return response()->json([
			'message' => 'Notification Sent successfully',
		], 200);
	}

	public function updateNotifications()
	{
		$notifications = $this->getNotifications();

		foreach ($notifications as $notification)
		{
			unset($notification['username']);
			$notification->update([
				'read_at' => now(),
			]);
		}

		return $notifications;
	}

	public function getUserNotifications()
	{
		$notifications = $this->getNotifications();

		return response()->json(['data' => $notifications], 200);
	}

	/**
	 * @return mixed
	 */
	public function getNotifications()
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
