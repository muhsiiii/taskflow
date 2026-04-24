<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'workspace_id',
        'created_by',
        'assignee_id',
        'parent_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'sort_order'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Project this task belongs to
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Workspace this task belongs to
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // Who created this task
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Who is assigned to this task
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // Parent task (if this is a subtask)
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // Child subtasks of this task
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // File attachments on this task
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // Scope: only top-level tasks
    public function scopeRootOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope: filter by status
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
