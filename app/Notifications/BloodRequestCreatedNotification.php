<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestCreatedNotification extends Notification
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
            'title_key' => 'notifications.request_pending_review',
            'blood_request_id' => $this->bloodRequest->id,
            'hospital_name' => $this->bloodRequest->hospital_name,
            'city' => $this->bloodRequest->city,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('O- Request Pending Review')
            ->line("A new O- blood request was submitted for {$this->bloodRequest->hospital_name}.")
            ->line("City: {$this->bloodRequest->city}")
            ->action('Open Admin Review', route('admin.requests.index'));
    }
}
