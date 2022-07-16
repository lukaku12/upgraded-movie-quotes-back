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

		$quote_author_id = Quote::where('id', $request['quote_id'])->get('user_id')->first()->user_id;

		if ($quote_author_id == auth()->user()->id)
		{
			return response()->json('no notification send for you', 200);
		}

		if ($request['message'] === 'Reacted to your quote')
		{
			if (Notification::where('user_id', $request['user_id'])->where('quote_id', $request['quote_id'])->exists())
			{
				return response()->json([
					'message' => 'Notification already exists',
					'status'  => 'error',
				], 400);
			}
		}

		$data = [
			'user_id'  => $request['user_id'],
			'quote_id' => $request['quote_id'],
			'message'  => $request['message'],
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

	public function update()
	{
		//        Notification::where('user_id', $request['user_id'])->where('quote_id', $request['quote_id'])->update(['read_at' => now()]);
	}

	public function getUserNotifications()
	{
		$user_notifications = Quote::where('user_id', auth()->user()->id)->with(['notifications'])->get();
		$user_notifications = $user_notifications->map(function ($item) {
			$item['notifications'] = $item->notifications->map(function ($item) {
				$item['username'] = $item->user->username;
				unset($item['user']);
				return $item;
			});
			return $item;
		});
		$notifications = $user_notifications->map(function ($item) {
			return $item->notifications;
		})->flatten();

		return response()->json(['data' => $notifications], 200);
	}
}
