<?php

namespace App\Livewire\Layout;

use Livewire\Attributes\On;
use Livewire\Component;


class NotificationBell extends Component
{
    public $unreadNotifications;
    public $unreadCount;


    #[On('notification-read')] // <-- Tambahkan listener ini
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (auth()->check()) {
            $this->unreadNotifications = auth()->user()->unreadNotifications;
            $this->unreadCount = auth()->user()->unreadNotifications()->count();
        }
    }

    public function markAsRead($notificationId)
    {
        if (auth()->check()) {
            $notification = auth()->user()->notifications()->find($notificationId);
            if ($notification) {
                // Simpan URL sebelum notifikasi dihapus
                $url = $notification->data['url'];

                // Tandai sebagai telah dibaca
                $notification->markAsRead();

                // Pindah halaman secara manual dari backend
                return $this->redirect($url, navigate: true);
            }
        }
    }

    public function render()
    {
        return view('livewire.layout.notification-bell');
    }
}
