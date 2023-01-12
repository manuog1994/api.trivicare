<?php

namespace App\Http\Controllers\Api\Notification;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function read(Notifications $notification)
    {
        $notification->read = true;
        $notification->save();
        return response()->json(['message' => 'Notificación leída'], 200);
    }

    public function destroy(Notifications $notification)
    {
        $notification->delete();
        return response()->json(['message' => 'Notificación eliminada'], 200);
    }

    public function show(User $user)
    {
        $notifications = Notifications::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $notifications,
            'message' => 'Notificaciones'
        ], 200);
    }

}
