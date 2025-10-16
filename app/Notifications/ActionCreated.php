<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Action;
use App\Models\MachineReport;

class ActionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $action;
    protected $report;

    public function __construct(Action $action, MachineReport $report)
    {
        $this->action = $action;
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('machine-reports.edit', $this->report->id);
        
        return (new MailMessage)
            ->subject('New Action Created for Machine Report')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new action has been created for your machine report.')
            ->line('Machine: ' . $this->report->machine_name)
            ->line('Action Description: ' . $this->action->description)
            ->line('Status: ' . $this->action->status)
            ->line('Technician: ' . $this->action->technician->name)
            ->action('View Report', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'action_id' => $this->action->action_id,
            'report_id' => $this->report->id,
            'machine_name' => $this->report->machine_name,
            'description' => $this->action->description,
            'status' => $this->action->status,
            'technician_name' => $this->action->technician->name,
            'message' => 'New action created for machine report: ' . $this->report->machine_name
        ];
    }
} 