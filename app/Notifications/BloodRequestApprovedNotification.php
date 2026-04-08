<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(protected BloodRequest $bloodRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return filled($notifiable->email) ? ['database', 'mail'] : ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage|array
    {
        return new DatabaseMessage([
            'title_key' => 'notifications.request_published',
            'blood_request_id' => $this->bloodRequest->id,
            'hospital_name' => $this->bloodRequest->hospital_name,
            'city' => $this->bloodRequest->city,
            'urgency_level' => $this->bloodRequest->urgency_level->value,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New O- Blood Request Published')
            ->line("A new verified O- blood request is now live for {$this->bloodRequest->hospital_name}.")
            ->line("City: {$this->bloodRequest->city}")
            ->action('View Published Requests', route('home'));
    }
}
