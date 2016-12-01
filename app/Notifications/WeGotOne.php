<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class WeGotOne extends Notification
{
    use Queueable;

    private $storeName;
    private $sale;
    private $available;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($storeName, $sale, $available)
    {
        $this->name = $storeName;
        $this->sale = $sale;
        $this->available = $available;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
                    ->success()
                    ->content($this->name.' has '.$this->sale.'-'. $this->available.' NES Classics');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'storeName' => $this->name
        ];
    }
}
