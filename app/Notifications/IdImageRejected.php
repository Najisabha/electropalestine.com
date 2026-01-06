<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IdImageRejected extends Notification
{
    use Queueable;

    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(?string $rejectionReason = null)
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم رفض صورة الهوية')
            ->line('عذراً، تم رفض صورة الهوية الخاصة بك.')
            ->line($this->rejectionReason ? 'السبب: ' . $this->rejectionReason : 'نرجو منك إعادة إرفاق صورة الهوية.')
            ->action('إرفاق صورة الهوية', route('store.account-settings'))
            ->line('شكراً لاستخدامك تطبيقنا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفض صورة الهوية',
            'message' => $this->rejectionReason 
                ? 'تم رفض صورة الهوية الخاصة بك. السبب: ' . $this->rejectionReason . ' نرجو إعادة إرفاق صورة الهوية من صفحة إعدادات الحساب.'
                : 'تم رفض صورة الهوية الخاصة بك. نرجو إعادة إرفاق صورة الهوية من صفحة إعدادات الحساب.',
            'type' => 'id_image_rejected',
            'action_url' => route('store.account-settings'),
        ];
    }
}
