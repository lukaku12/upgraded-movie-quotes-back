<?php

namespace App\Http\Controllers;

use App\Events\NotifyUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class NotificationController extends Controller
{
    public function index()
    {
        $request = request()->all();
        if($request['message'] === 'Reacted to your quote') {
             if( Notification::where('user_id', $request['user_id'])->where('quote_id', $request['quote_id'])->exists()) {
                return response()->json([
                    'message' => 'Notification already exists',
                    'status' => 'error',
                    ], 400);
             }
        }


        $data = [
            'user_id' => $request['user_id'],
            'quote_id' => $request['quote_id'],
            'message' => $request['message'],
        ];

        $message = Notification::create($data);
        $message['username'] = $message->user->username;
        unset($message['user']);
        broadcast(
           (new NotifyUser(array($message)))->dontBroadcastToCurrentUser()
        );
        return response()->json([
            'message' => 'Notification Sent successfully',
        ], 200);
    }

    public function update()
    {
//        Notification::where('user_id', $request['user_id'])->where('quote_id', $request['quote_id'])->update(['read_at' => now()]);
    }
}

