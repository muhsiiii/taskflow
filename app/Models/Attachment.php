<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'filename',
        's3_key',
        'mime_type',
        'size_bytes'
    ];

    // Task this file is attached to
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Who uploaded this file
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
