<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SparePart;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $sparePart;

    public function __construct(SparePart $sparePart)
    {
        $this->sparePart = $sparePart;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->sparePart->name)
            ->greeting('Hello!')
            ->line('This is to inform you that the following spare part is running low on stock:')
            ->line('Spare Part: ' . $this->sparePart->name)
            ->line('Current Quantity: ' . $this->sparePart->quantity)
            ->action('View Spare Part', route('spare-parts.edit', $this->sparePart->id))
            ->line('Please take necessary action to replenish the stock.');
    }

    public function toArray($notifiable)
    {
        return [
            'spare_part_id' => $this->sparePart->id,
            'spare_part_name' => $this->sparePart->name,
            'quantity' => $this->sparePart->quantity,
            'message' => 'Low stock alert for ' . $this->sparePart->name . ' (Current quantity: ' . $this->sparePart->quantity . ')'
        ];
    }
} 