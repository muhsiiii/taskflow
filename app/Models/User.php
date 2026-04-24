<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Workspaces this user owns
    public function ownedWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    // Workspaces this user is a member of
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Tasks assigned to this user
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    // Tasks created by this user
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    // Helper: get role in a specific workspace
    public function roleInWorkspace(int $workspaceId): ?string
    {
        $pivot = $this->workspaces()
            ->where('workspace_id', $workspaceId)
            ->first()?->pivot;
        return $pivot?->role;
    }
}
