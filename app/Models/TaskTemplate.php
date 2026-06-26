<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'status',
        'estimate_minutes',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
