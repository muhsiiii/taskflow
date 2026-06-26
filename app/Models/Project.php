<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'created_by',
        'name',
        'description',
        'status',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Which workspace this project belongs to
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // Who created this project
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // All tasks in this project
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Only top-level tasks (no parent)
    public function rootTasks()
    {
        return $this->hasMany(Task::class)->whereNull('parent_id');
    }

    // Project team members
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
