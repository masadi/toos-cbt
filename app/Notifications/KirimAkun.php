<?php

namespace App\Notifications;
use App\Channels\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;

class KirimAkun extends Notification
{
    use Queueable;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $orderUrl = url("/kirim-wa/{$notifiable->user_id}");
        $company = 'CV. Cyber Electra';
        $cbtUrl = url();
        return (new WhatsAppMessage)->content("Informasi untuk login di aplikasi CBT, silahkan menggunakan username *{$notifiable->username}* dan password *{$notifiable->default_password}*");
        //return (new WhatsAppMessage)->content("Your {$company} on {$notifiable->default_password}. Details: {$orderUrl}");
    }
}
