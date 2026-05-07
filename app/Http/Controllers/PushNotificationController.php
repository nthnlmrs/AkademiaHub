<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminBroadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|in:all,lecturer,student',
        ]);

        $users = User::query();
        if ($request->target === 'lecturer') {
            $users->where('role', 'lecturer');
        } elseif ($request->target === 'student') {
            $users->where('role', 'student');
        }

        Notification::send($users->get(), new AdminBroadcast($request->title, $request->message, Auth::user()->name));

        return back()->with('success', 'Notification pushed successfully!');
    }

    public function markRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}
