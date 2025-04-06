<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function readNotification(Request $request, $notification_id){
        $notification = DatabaseNotification::where('id', $notification_id)->first();
        $notification->markAsRead(); 
        return Auth::user()->unreadNotifications;
    }
}
