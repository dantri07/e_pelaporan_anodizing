<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MachineReport;

class MachineReportAssigned extends Notification
{
    use Queueable;

    protected $machineReport;

    /**
     * Create a new notification instance.
     */
    public function __construct(MachineReport $machineReport)
    {
        $this->machineReport = $machineReport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Machine Report Assigned')
            ->greeting('Hello, ' . $notifiable->name)
            ->line('You have been assigned to handle the following machine report:')
            ->line('Machine: ' . $this->machineReport->machine_name)
            ->line('Description: ' . $this->machineReport->report_description)
            ->action('View Report', url('/machine-reports/' . $this->machineReport->id))
            ->line('Please check and follow up as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'machine_report_id' => $this->machineReport->id,
            'machine_name' => $this->machineReport->machine_name,
            'report_description' => $this->machineReport->report_description,
            'created_by' => $this->machineReport->user->name ?? '',
            'url' => url('/machine-reports/' . $this->machineReport->id),
            'message' => 'You have been assigned a new machine report: ' . $this->machineReport->machine_name,
        ];
    }
}
