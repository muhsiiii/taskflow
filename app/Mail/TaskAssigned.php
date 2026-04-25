<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly User $assignee
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been assigned: {$this->task->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.task-assigned',
            with: [
                'taskTitle'    => $this->task->title,
                'taskPriority' => $this->task->priority,
                'taskStatus'   => $this->task->status,
                'assigneeName' => $this->assignee->name,
                'projectName'  => $this->task->project->name ?? 'Unknown Project',
            ],
        );
    }
}
