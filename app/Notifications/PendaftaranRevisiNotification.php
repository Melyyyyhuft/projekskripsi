<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendaftaranRevisiNotification extends Notification
{
    use Queueable;

    public $pendaftaran;

    public function __construct($pendaftaran)
    {
        $this->pendaftaran = $pendaftaran;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pendaftaran_id' => $this->pendaftaran->id,
            'nama_siswa' => $this->pendaftaran->user->name,
            'pesan' => $this->pendaftaran->user->name . ' telah mengupload revisi berkas',
            'type' => 'revisi',
        ];
    }
}
