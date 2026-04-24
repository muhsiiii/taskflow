<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'owner_id'];

    // Owner of this workspace
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // All members (via pivot table)
    public function members()
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // All projects in this workspace
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // All tasks in this workspace
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
