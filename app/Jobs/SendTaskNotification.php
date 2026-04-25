<?php

namespace App\Jobs;

use App\Mail\TaskAssigned;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTaskNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Retry up to 3 times if it fails
    public int $tries = 3;

    // Wait 60 seconds between retries
    public int $backoff = 60;

    public function __construct(
        public readonly Task $task,
        public readonly User $assignee
    ) {}

    public function handle(): void
    {
        Log::info("Sending task assignment email", [
            'task_id'     => $this->task->id,
            'task_title'  => $this->task->title,
            'assignee_id' => $this->assignee->id,
            'assignee'    => $this->assignee->email,
        ]);

        Mail::to($this->assignee->email)
            ->send(new TaskAssigned($this->task, $this->assignee));

        Log::info("Task assignment email sent successfully", [
            'task_id' => $this->task->id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send task assignment email", [
            'task_id' => $this->task->id,
            'error'   => $exception->getMessage(),
        ]);
    }
}
