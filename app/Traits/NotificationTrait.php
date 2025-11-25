<?php

namespace App\Traits;

trait NotificationTrait
{
    public static function channels($notifiable)
    {
        $via = ['database'];
        if ($notifiable->prefers_sms) {
            $via[] = 'sms';
        }
        if ($notifiable->prefers_email) {
            $via[] = 'mail';
        }

        return $via;
    }
}
