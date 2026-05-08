<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([ 'status' => 'success','data' => $request->user()->notifications]);
    }

    public function unread(Request $request)
    {
        return response()->json(['status' => 'success','data' => $request->user()->unreadNotifications]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['status' => 'error','message' => 'Notification not found',], 404);
        }

        $notification->markAsRead();
        return response()->json(['status' => 'success','message' => 'Notification marked as read',]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success','message' => 'All notifications marked as read',]);
    }

}