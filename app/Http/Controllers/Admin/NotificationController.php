<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'unread');

        if ($tab == 'unread') {
            $notifications = $user->unreadNotifications;
        } else {
            $notifications = $user->readNotifications;
        }

        return view('admin.notifikasi.index', compact('notifications', 'tab'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Ambil data terkait (misalnya pendaftaran_id) untuk redirect
        if (isset($notification->data['pendaftaran_id'])) {
            return redirect()->route('admin.pendaftaran.index', ['tab' => 'baru']);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
