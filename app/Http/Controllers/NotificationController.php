<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function show(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === $request->user()->id, 404);
        $notification->markAsRead();

        return redirect()->to($notification->data['url'] ?? route('dashboard'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
